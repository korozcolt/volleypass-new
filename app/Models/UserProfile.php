<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\HasSearch;

class UserProfile extends Model
{
    use LogsActivity, HasSearch;

    protected $fillable = [
        'user_id',
        'nickname',
        'bio',
        'joined_date',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'blood_type',
        'allergies',
        'medical_conditions',
        'medications',
        't_shirt_size',
        'social_media',
        'notes',
        'show_phone',
        'show_email',
        'show_address',
    ];

    protected $casts = [
        'joined_date' => 'date',
        'social_media' => 'array',
        'show_phone' => 'boolean',
        'show_email' => 'boolean',
        'show_address' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $searchable = ['nickname', 'user.name', 'user.email'];

    // =======================
    // SPATIE ACTIVITY LOG
    // =======================

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nickname', 'bio', 'emergency_contact_name', 'blood_type'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => match ($eventName) {
                'created' => 'Perfil de usuario creado',
                'updated' => 'Perfil de usuario actualizado',
                'deleted' => 'Perfil de usuario eliminado',
                default => "Perfil {$eventName}"
            });
    }

    // =======================
    // RELACIONES
    // =======================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // =======================
    // ACCESSORS
    // =======================

    public function getDisplayNameAttribute(): string
    {
        return $this->nickname ?: $this->user->full_name;
    }

    public function getYearsPlayingAttribute(): ?int
    {
        return $this->joined_date ? $this->joined_date->diffInYears(now()) : null;
    }

    public function getEmergencyContactAttribute(): ?string
    {
        if (!$this->emergency_contact_name) {
            return null;
        }

        $contact = $this->emergency_contact_name;
        if ($this->emergency_contact_relationship) {
            $contact .= " ({$this->emergency_contact_relationship})";
        }
        if ($this->emergency_contact_phone) {
            $contact .= " - {$this->emergency_contact_phone}";
        }

        return $contact;
    }

    public function getSocialMediaLinksAttribute(): array
    {
        if (!$this->social_media) {
            return [];
        }

        $links = [];
        foreach ($this->social_media as $platform => $username) {
            if (empty($username)) continue;

            $links[$platform] = match ($platform) {
                'instagram' => "https://instagram.com/{$username}",
                'facebook' => "https://facebook.com/{$username}",
                'twitter' => "https://twitter.com/{$username}",
                'tiktok' => "https://tiktok.com/@{$username}",
                default => $username
            };
        }

        return $links;
    }

    // =======================
    // MÉTODOS DE PRIVACIDAD
    // =======================

    public function canShowPhone($viewer = null): bool
    {
        if (!$viewer) return false;

        // El mismo usuario siempre puede ver su info
        if ($this->user_id === $viewer->id) return true;

        // Super admin ve todo
        if ($viewer->isSuperAdmin()) return true;

        // Verificar configuración de privacidad
        return $this->show_phone;
    }

    public function canShowEmail($viewer = null): bool
    {
        if (!$viewer) return false;

        if ($this->user_id === $viewer->id) return true;
        if ($viewer->isSuperAdmin()) return true;

        return $this->show_email;
    }

    public function canShowAddress($viewer = null): bool
    {
        if (!$viewer) return false;

        if ($this->user_id === $viewer->id) return true;
        if ($viewer->isSuperAdmin()) return true;

        return $this->show_address;
    }

    // =======================
    // MÉTODOS DE UTILIDAD
    // =======================

    public function hasMedicalInfo(): bool
    {
        return !empty($this->blood_type) ||
            !empty($this->allergies) ||
            !empty($this->medical_conditions) ||
            !empty($this->medications);
    }

    public function hasEmergencyContact(): bool
    {
        return !empty($this->emergency_contact_name) &&
            !empty($this->emergency_contact_phone);
    }

    public function updateSocialMedia(string $platform, ?string $username): void
    {
        $socialMedia = $this->social_media ?? [];

        if (empty($username)) {
            unset($socialMedia[$platform]);
        } else {
            $socialMedia[$platform] = $username;
        }

        $this->update(['social_media' => $socialMedia]);
    }
}
