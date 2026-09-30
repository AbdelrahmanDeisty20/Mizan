<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Hearing extends Model
{
    use HasFactory;

    protected $fillable = [
        'office_id',
        'legal_case_id',
        'assigned_lawyer_id',
        'hearing_date',
        'court_room',
        'decision',
        'requirements',
        'status',
    ];

    protected $casts = [
        'hearing_date' => 'datetime',
    ];

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function legalCase(): BelongsTo
    {
        return $this->belongsTo(LegalCase::class);
    }

    public function assignedLawyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_lawyer_id');
    }
}
