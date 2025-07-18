<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FederationService;
use App\Services\PaymentValidationService;
use App\Models\Player;
use App\Models\Club;
use App\Models\League;
use App\Models\Payment;
use App\Models\User;
use App\Enums\FederationStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;

class TestFederationSystem extends Command
{
    protected $signature = 'volleypass:test-federation {--reset : Reset test data}';

    protected $description = 'Test the federation system functionality';

    public function handle()
    {
        $this->info('🏐 Testing VolleyPass Federation System');
        $this->newLine();

        if ($this->option('reset')) {
            $this->resetTestData();
        }

        $this->testFederationService();
        $this->testPaymentValidationService();
        $this->displaySystemStats();

        $this->newLine();
        $this->info('✅ Federation system test completed successfully!');
    }

    private function testFederationService()
    {
        $this->info('🔍 Testing Federation Service...');

        $federationService = app(FederationService::class);

        // Test 1: Get general stats
        $stats = $federationService->getGeneralFederationStats();
        $this->line("📊 General Stats:");
        $this->line("   Total Players: {$stats['total_players']}");
        $this->line("   Federated: {$stats['federated']} ({$stats['federation_percentage']}%)");
        $this->line("   Not Federated: {$stats['not_federated']}");
        $this->line("   Pending Payment: {$stats['pending_payment']}");
        $this->line("   Expired: {$stats['expired']}");

        // Test 2: Check expiring federations
        $expiring = $federationService->getPlayersExpiringFederation(30);
        $this->line("⏰ Players with federation expiring in 30 days: {$expiring->count()}");

        // Test 3: Update expired federations
        $updateResult = $federationService->updateExpiredFederations();
        $this->line("🔄 Updated expired federations: {$updateResult['updated']}");

        $this->newLine();
    }

    private function testPaymentValidationService()
    {
        $this->info('💰 Testing Payment Validation Service...');

        $paymentService = app(PaymentValidationService::class);

        // Test 1: Get pending payments
        $pendingPayments = $paymentService->getPendingPayments();
        $this->line("📋 Pending payments: {$pendingPayments->count()}");

        // Test 2: Get payment stats
        $stats = $paymentService->getPaymentStats();
        $this->line("📊 Payment Stats:");
        $this->line("   Total: {$stats['total']}");
        $this->line("   Pending: {$stats['pending']}");
        $this->line("   Verified: {$stats['verified']}");
        $this->line("   Rejected: {$stats['rejected']}");
        $this->line("   Approval Rate: {$stats['approval_rate']}%");

        $this->newLine();
    }

    private function displaySystemStats()
    {
        $this->info('📈 Current System Status:');

        // Players by federation status
        $this->line('Players by Federation Status:');
        foreach (FederationStatus::cases() as $status) {
            $count = Player::where('federation_status', $status)->count();
            $this->line("   {$status->getLabel()}: {$count}");
        }

        $this->newLine();

        // Payments by status
        $this->line('Payments by Status:');
        foreach (PaymentStatus::cases() as $status) {
            $count = Payment::where('status', $status)->count();
            $this->line("   {$status->getLabel()}: {$count}");
        }

        $this->newLine();

        // Clubs stats
        $totalClubs = Club::count();
        $activeClubs = Club::where('is_active', true)->count();
        $clubsWithDirector = Club::whereNotNull('director_id')->count();

        $this->line('Clubs Status:');
        $this->line("   Total Clubs: {$totalClubs}");
        $this->line("   Active Clubs: {$activeClubs}");
        $this->line("   Clubs with Director: {$clubsWithDirector}");

        $this->newLine();
    }

    private function resetTestData()
    {
        $this->warn('🔄 Resetting test data...');

        // Reset all players to not federated
        Player::query()->update([
            'federation_status' => FederationStatus::NotFederated,
            'federation_date' => null,
            'federation_expires_at' => null,
            'federation_payment_id' => null,
            'federation_notes' => null,
        ]);

        // Reset all payments to pending
        Payment::query()->update([
            'status' => PaymentStatus::Pending,
            'verified_at' => null,
            'verified_by' => null,
        ]);

        $this->info('✅ Test data reset completed');
        $this->newLine();
    }
}
