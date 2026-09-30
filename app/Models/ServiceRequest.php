<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'requester_office_id',
        'assigned_office_id',
        'title',
        'description',
        'governorate_id',
        'court_name',
        'due_date',
        'offered_fee',
        'status',
        'contact_phone',
    ];

    protected $casts = [
        'due_date' => 'date',
        'offered_fee' => 'decimal:2',
    ];

    public function requesterOffice(): BelongsTo
    {
        return $this->belongsTo(Office::class, 'requester_office_id');
    }

    public function assignedOffice(): BelongsTo
    {
        return $this->belongsTo(Office::class, 'assigned_office_id');
    }

    public function governorate(): BelongsTo
    {
        return $this->belongsTo(Governorate::class);
    }
}
