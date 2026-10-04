<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'hearing_time',
        'hearing_type_id',
        'court_room',
        'roll_number',
        'decision',
        'requirements',
        'status',
    ];

    protected $casts = [
        'hearing_date' => 'date:Y-m-d',
    ];

    /**
     * Mutator to automatically format incoming date to YYYY-MM-DD for MySQL.
     */
    protected function hearingDate(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value ? Carbon::parse($value)->format('Y-m-d') : null,
        );
    }

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

    public function hearingType(): BelongsTo
    {
        return $this->belongsTo(HearingType::class);
    }
}
