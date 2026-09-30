<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Office extends Model
{
    use HasFactory;

    protected $fillable = [
        'office_name',
        'syndicate_card_id',
        'degree_id',
        'governorate_id',
        'office_address',
        'office_phone',
        'address',
        'phone',
        'logo_path',
        'trial_ends_at',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
    ];

    public function degree(): BelongsTo
    {
        return $this->belongsTo(Degree::class);
    }

    public function governorate(): BelongsTo
    {
        return $this->belongsTo(Governorate::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    public function legalCases(): HasMany
    {
        return $this->hasMany(LegalCase::class);
    }

    public function hearings(): HasMany
    {
        return $this->hasMany(Hearing::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function financialReceipts(): HasMany
    {
        return $this->hasMany(FinancialReceipt::class);
    }

    public function requestedServices(): HasMany
    {
        return $this->hasMany(ServiceRequest::class, 'requester_office_id');
    }

    public function assignedServices(): HasMany
    {
        return $this->hasMany(ServiceRequest::class, 'assigned_office_id');
    }

    public function isTrialActive(): bool
    {
        if (! $this->trial_ends_at) {
            return false;
        }

        return now()->lessThanOrEqualTo($this->trial_ends_at);
    }
}
