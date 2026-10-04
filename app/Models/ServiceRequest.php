<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'requester_office_id',
        'assigned_office_id',
        'user_id',
        'assigned_user_id',
        'title',
        'description',
        'governorate_id',
        'court_id',
        'case_number',
        'due_date',
        'offered_fee',
        'status',
        'contact_phone',
    ];

    protected $casts = [
        'due_date'    => 'date:Y-m-d',
        'offered_fee' => 'decimal:2',
    ];

    /**
     * Mutator to automatically format incoming date to YYYY-MM-DD for MySQL.
     */
    protected function dueDate(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value ? Carbon::parse($value)->format('Y-m-d') : null,
        );
    }

    public function requesterOffice(): BelongsTo
    {
        return $this->belongsTo(Office::class, 'requester_office_id');
    }

    public function assignedOffice(): BelongsTo
    {
        return $this->belongsTo(Office::class, 'assigned_office_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function governorate(): BelongsTo
    {
        return $this->belongsTo(Governorate::class);
    }

    public function court(): BelongsTo
    {
        return $this->belongsTo(Court::class);
    }

    public function offers(): HasMany
    {
        return $this->hasMany(ServiceRequestOffer::class);
    }
}
