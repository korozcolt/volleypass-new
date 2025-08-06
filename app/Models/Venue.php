<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Venue extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'capacity',
        'court_count',
        'facilities',
        'contact_phone',
        'contact_email',
        'is_active',
        'latitude',
        'longitude',
        'description',
        'amenities',
        'parking_available',
        'accessibility_features',
        'rental_cost_per_hour',
        'availability_schedule',
        'notes',
        'metadata'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'parking_available' => 'boolean',
        'facilities' => 'array',
        'amenities' => 'array',
        'accessibility_features' => 'array',
        'availability_schedule' => 'array',
        'rental_cost_per_hour' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'metadata' => 'array'
    ];

    // Relationships
    public function matches(): HasMany
    {
        return $this->hasMany(VolleyMatch::class);
    }

    public function tournaments(): HasMany
    {
        return $this->hasMany(Tournament::class, 'venue_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCity($query, string $city)
    {
        return $query->where('city', $city);
    }

    public function scopeWithMinCapacity($query, int $minCapacity)
    {
        return $query->where('capacity', '>=', $minCapacity);
    }

    // Helper methods
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country
        ]);
        
        return implode(', ', $parts);
    }

    public function isAvailable(\DateTime $dateTime): bool
    {
        // Verificar si el venue está activo
        if (!$this->is_active) {
            return false;
        }

        // Verificar disponibilidad básica
        // Aquí se podría implementar lógica más compleja
        // basada en availability_schedule y partidos existentes
        
        return true;
    }

    public function getCapacityStatusAttribute(): string
    {
        if ($this->capacity >= 1000) {
            return 'large';
        } elseif ($this->capacity >= 500) {
            return 'medium';
        } elseif ($this->capacity >= 100) {
            return 'small';
        }
        
        return 'minimal';
    }

    public function hasAmenity(string $amenity): bool
    {
        return in_array($amenity, $this->amenities ?? []);
    }

    public function hasFacility(string $facility): bool
    {
        return in_array($facility, $this->facilities ?? []);
    }

    public function getCoordinatesAttribute(): ?array
    {
        if ($this->latitude && $this->longitude) {
            return [
                'lat' => (float) $this->latitude,
                'lng' => (float) $this->longitude
            ];
        }
        
        return null;
    }

    public function getDistanceFrom(float $latitude, float $longitude): ?float
    {
        if (!$this->latitude || !$this->longitude) {
            return null;
        }

        // Fórmula de Haversine para calcular distancia
        $earthRadius = 6371; // Radio de la Tierra en kilómetros
        
        $latFrom = deg2rad($latitude);
        $lonFrom = deg2rad($longitude);
        $latTo = deg2rad($this->latitude);
        $lonTo = deg2rad($this->longitude);
        
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
        
        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c;
    }
}