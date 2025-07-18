<?php

namespace Tests\Feature\Federation;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use App\Services\PaymentValidationService;
use App\Models\Payment;
use App\Models\Club;
use App\Models\League;
use App\Models\User;
use App\Models\Country;
use App\Models\Department;
use App\Models\City;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Enums\UserStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PaymentValidationServiceTest extends TestCase
{
    use RefreshDatabase;

    private PaymentValidationService $paymentService;
    private League $league;
    private Club $club;
    private User $director;
    private Payment $payment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->paymentService = app(PaymentValidationService::class);
        $this->createTestData();
    }

    private function createTestData(): void
    {
        // Crear estructura geográfica
        $country = Country::create([
            'name' => 'Colombia',
            'code' => 'CO',
            'phone_code' => '+57',
            'currency_code' => 'COP',
            'is_active' => true,
        ]);

        $department = Department::create([
            'country_id' => $country->id,
            'name' => 'Bogotá D.C.',
            'code' => 'DC',
            'is_active' => true,
        ]);

        $city = City::create([
            'department_id' => $department->id,
            'name' => 'Bogotá',
            'code' => 'BOG',
            'postal_code' => '110111',
            'is_active' => true,
        ]);

        // Crear liga
        $this->league = League::create([
            'name' => 'Liga Test',
            'short_name' => 'LT',
            'description' => 'Liga para testing',
            'city_id' => $city->id,
            'department_id' => $department->id,
            'country_id' => $country->id,
            'status' => UserStatus::Active,
            'is_active' => true,
            'email' => 'liga@test.com',
            'phone' => '3001234567',
            'configurations' => [
                'federation_fee' => 50000,
            ],
        ]);

        // Crear director
        $this->director = User::create([
            'name' => 'Director Test',
            'first_name' => 'Director',
            'last_name' => 'Test',
            'email' => 'director@test.com',
            'document_number' => '12345678',
            'phone' => '3001234567',
            'status' => UserStatus::Active,
            'league_id' => $this->league->id,
            'password' => bcrypt('password'),
        ]);

        // Crear club
        $this->club = Club::create([
            'league_id' => $this->league->id,
            'name' => 'Club Test',
            'short_name' => 'CT',
            'description' => 'Club para testing',
            'city_id' => $city->id,
            'email' => 'club@test.com',
            'phone' => '3001234567',
            'director_id' => $this->director->id,
            'status' => UserStatus::Active,
            'is_active' => true,
        ]);

        $this->director->update(['club_id' => $this->club->id]);

        // Crear pago
        $this->payment = Payment::create([
            'club_id' => $this->club->id,
            'league_id' => $this->league->id,
            'user_id' => $this->director->id,
            'type' => PaymentType::Federation,
            'amount' => 50000,
            'currency' => 'COP',
            'reference_number' => 'TEST-001',
            'payment_method' => 'transfer',
            'status' => PaymentStatus::Pending,
            'paid_at' => now(),
        ]);
    }

    #[Test]
    public function it_can_validate_federation_payment()
    {
        $validation = $this->paymentService->validateFederationPayment($this->payment);

        $this->assertTrue($validation['is_valid']);
        $this->assertTrue($validation['can_approve']);
        $this->assertEmpty($validation['errors']);
    }

    #[Test]
    public function it_validates_payment_type()
    {
        $this->payment->update(['type' => PaymentType::Registration]);

        $validation = $this->paymentService->validateFederationPayment($this->payment);

        $this->assertFalse($validation['is_valid']);
        $this->assertContains('El pago debe ser de tipo federación', $validation['errors']);
    }

    #[Test]
    public function it_validates_payment_amount()
    {
        $this->payment->update(['amount' => 0]);

        $validation = $this->paymentService->validateFederationPayment($this->payment);

        $this->assertFalse($validation['is_valid']);
        $this->assertContains('El monto debe ser mayor a cero', $validation['errors']);
    }

    #[Test]
    public function it_validates_reference_number()
    {
        $this->payment->update(['reference_number' => '']);

        $validation = $this->paymentService->validateFederationPayment($this->payment);

        $this->assertFalse($validation['is_valid']);
        $this->assertContains('Debe tener número de referencia', $validation['errors']);
    }

    #[Test]
    public function it_validates_club_association()
    {
        $this->payment->update(['club_id' => null]);

        $validation = $this->paymentService->validateFederationPayment($this->payment);

        $this->assertFalse($validation['is_valid']);
        $this->assertContains('Debe estar asociado a un club', $validation['errors']);
    }

    #[Test]
    public function it_validates_league_association()
    {
        $this->payment->update(['league_id' => null]);

        $validation = $this->paymentService->validateFederationPayment($this->payment);

        $this->assertFalse($validation['is_valid']);
        $this->assertContains('Debe estar asociado a una liga', $validation['errors']);
    }

    #[Test]
    public function it_detects_duplicate_reference_numbers()
    {
        // Crear otro pago con el mismo número de referencia
        Payment::create([
            'club_id' => $this->club->id,
            'league_id' => $this->league->id,
            'user_id' => $this->director->id,
            'type' => PaymentType::Federation,
            'amount' => 50000,
            'currency' => 'COP',
            'reference_number' => 'TEST-001', // Mismo número
            'payment_method' => 'transfer',
            'status' => PaymentStatus::Verified,
            'paid_at' => now(),
        ]);

        $validation = $this->paymentService->validateFederationPayment($this->payment);

        $this->assertFalse($validation['is_valid']);
        $this->assertContains('Ya existe un pago con este número de referencia', $validation['errors']);
    }

    #[Test]
    public function it_can_approve_valid_payment()
    {
        $approver = User::create([
            'name' => 'Approver Test',
            'email' => 'approver@test.com',
            'document_number' => '99999999',
            'password' => bcrypt('password'),
            'status' => UserStatus::Active,
        ]);
        $notes = 'Pago aprobado en testing';

        $result = $this->paymentService->approvePayment($this->payment, $approver, $notes);

        $this->assertTrue($result);
        $this->payment->refresh();

        $this->assertEquals(PaymentStatus::Verified, $this->payment->status);
        $this->assertNotNull($this->payment->verified_at);
        $this->assertEquals($approver->id, $this->payment->verified_by);
        $this->assertStringContainsString($notes, $this->payment->notes);
    }

    #[Test]
    public function it_cannot_approve_invalid_payment()
    {
        $this->payment->update(['amount' => 0]); // Hacer inválido
        $approver = User::create([
            'name' => 'Invalid Approver',
            'email' => 'invalid@test.com',
            'document_number' => '88888888',
            'password' => bcrypt('password'),
            'status' => UserStatus::Active,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('El pago no puede ser aprobado');

        $this->paymentService->approvePayment($this->payment, $approver);
    }

    #[Test]
    public function it_can_reject_payment()
    {
        $rejector = User::create([
            'name' => 'Rejector Test',
            'email' => 'rejector@test.com',
            'document_number' => '77777777',
            'password' => bcrypt('password'),
            'status' => UserStatus::Active,
        ]);
        $reason = 'Comprobante ilegible';

        $result = $this->paymentService->rejectPayment($this->payment, $rejector, $reason);

        $this->assertTrue($result);
        $this->payment->refresh();

        $this->assertEquals(PaymentStatus::Rejected, $this->payment->status);
        $this->assertNotNull($this->payment->verified_at);
        $this->assertEquals($rejector->id, $this->payment->verified_by);
        $this->assertStringContainsString($reason, $this->payment->notes);
    }

    #[Test]
    public function it_can_get_pending_payments()
    {
        // Crear pagos adicionales con diferentes estados
        Payment::factory()->create([
            'league_id' => $this->league->id,
            'status' => PaymentStatus::Pending,
        ]);

        Payment::factory()->create([
            'league_id' => $this->league->id,
            'status' => PaymentStatus::Verified,
        ]);

        $pendingPayments = $this->paymentService->getPendingPayments($this->league);

        $this->assertEquals(2, $pendingPayments->count()); // payment original + 1 creado
    }

    #[Test]
    public function it_can_get_payment_stats()
    {
        // Crear pagos con diferentes estados
        Payment::factory()->create([
            'league_id' => $this->league->id,
            'status' => PaymentStatus::Verified,
        ]);

        Payment::factory()->create([
            'league_id' => $this->league->id,
            'status' => PaymentStatus::Rejected,
        ]);

        $stats = $this->paymentService->getPaymentStats($this->league);

        $this->assertIsArray($stats);
        $this->assertEquals(3, $stats['total']); // payment original + 2 creados
        $this->assertEquals(1, $stats['pending']);
        $this->assertEquals(1, $stats['verified']);
        $this->assertEquals(1, $stats['rejected']);
        $this->assertEquals(50.0, $stats['approval_rate']); // 1 verified / 2 processed
    }

    #[Test]
    public function it_can_validate_payment_amount_against_league_configuration()
    {
        $validation = $this->paymentService->validatePaymentAmount($this->payment);

        $this->assertEmpty($validation['errors']);
        $this->assertEquals(50000, $validation['expected_amount']);

        // Cambiar monto a uno incorrecto
        $this->payment->update(['amount' => 30000]);

        $validation = $this->paymentService->validatePaymentAmount($this->payment);

        $this->assertNotEmpty($validation['errors']);
        $this->assertContains('Monto insuficiente', $validation['errors'][0]);
    }

    #[Test]
    public function it_can_validate_payment_integrity()
    {
        $validation = $this->paymentService->validatePaymentIntegrity($this->payment);

        $this->assertTrue($validation['is_valid']);
        $this->assertEmpty($validation['issues']);

        // Crear inconsistencia
        $this->payment->update([
            'verified_at' => now(),
            'verified_by' => null, // Inconsistencia
        ]);

        $validation = $this->paymentService->validatePaymentIntegrity($this->payment);

        $this->assertFalse($validation['is_valid']);
        $this->assertContains('Pago marcado como verificado pero sin verificador', $validation['issues']);
    }

    #[Test]
    public function it_can_process_receipt_file()
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->image('receipt.jpg', 800, 600);

        $result = $this->paymentService->processReceiptFile($this->payment, $file);

        $this->assertTrue($result);
        $this->assertCount(1, $this->payment->getMedia('receipts'));
    }

    #[Test]
    public function it_validates_receipt_file_type()
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->create('document.txt', 100);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Tipo de archivo no permitido');

        $this->paymentService->processReceiptFile($this->payment, $file);
    }

    #[Test]
    public function it_validates_receipt_file_size()
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->create('receipt.jpg', 6000); // 6MB

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('El archivo es demasiado grande');

        $this->paymentService->processReceiptFile($this->payment, $file);
    }

    #[Test]
    public function it_can_generate_payment_report()
    {
        // Crear pagos adicionales
        Payment::factory()->count(3)->create([
            'league_id' => $this->league->id,
            'created_at' => now()->subDays(5),
        ]);

        $startDate = now()->subDays(10);
        $endDate = now();

        $report = $this->paymentService->generatePaymentReport($this->league, $startDate, $endDate);

        $this->assertIsArray($report);
        $this->assertArrayHasKey('period', $report);
        $this->assertArrayHasKey('league', $report);
        $this->assertArrayHasKey('summary', $report);
        $this->assertArrayHasKey('details', $report);

        $this->assertEquals($this->league->name, $report['league']);
        $this->assertEquals(4, $report['summary']['total_payments']); // payment original + 3 creados
    }
}
