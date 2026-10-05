<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Consultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'consultation_number',
        'client_id',
        'office_id',
        'user_id',
        'consultation_method',
        'preferred_date',
        'preferred_time',
        'subject',
        'status',
        'reply',
        'fee',
        'is_paid',
    ];

    protected $casts = [
        'preferred_date' => 'date:Y-m-d',
        'fee'            => 'decimal:2',
        'is_paid'        => 'boolean',
    ];

    /**
     * Mutator to format preferred_date to YYYY-MM-DD.
     */
    protected function preferredDate(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value ? Carbon::parse($value)->format('Y-m-d') : null,
        );
    }

    /**
     * Booted event listener for automatic consultation_number generation.
     */
    protected static function booted(): void
    {
        static::creating(function (Consultation $consultation) {
            if (empty($consultation->consultation_number)) {
                $consultation->consultation_number = static::generateConsultationNumber();
            }
        });
    }

    /**
     * Generate unique consultation number (e.g. CNS-2026-0001).
     */
    public static function generateConsultationNumber(): string
    {
        $year       = date('Y');
        $nextId     = (static::max('id') ?? 0) + 1;
        $codeNumber = sprintf('CNS-%s-%04d', $year, $nextId);

        while (static::where('consultation_number', $codeNumber)->exists()) {
            $nextId++;
            $codeNumber = sprintf('CNS-%s-%04d', $year, $nextId);
        }

        return $codeNumber;
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
