<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialReceipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'office_id',
        'client_id',
        'legal_case_id',
        'user_id',
        'receipt_number',
        'amount',
        'payment_method',
        'notes',
        'date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date'   => 'date:Y-m-d',
    ];

    /**
     * Mutator to automatically format incoming date to YYYY-MM-DD for MySQL.
     */
    protected function date(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value ? Carbon::parse($value)->format('Y-m-d') : null,
        );
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function legalCase(): BelongsTo
    {
        return $this->belongsTo(LegalCase::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
