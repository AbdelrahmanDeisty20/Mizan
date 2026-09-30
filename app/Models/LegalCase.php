<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Court;

class LegalCase extends Model
{
    use HasFactory;

    protected $fillable = [
        'office_id',
        'client_id',
        'client_role',
        'court_id',
        'case_number',
        'year',
        'degree',
        'case_type',
        'status',
        'opponent_name',
        'opponent_lawyer',
        'total_fees',
        'paid_fees',
        'remaining_fees',
        'notes',
    ];

    protected $casts = [
        'year' => 'integer',
        'total_fees' => 'decimal:2',
        'paid_fees' => 'decimal:2',
        'remaining_fees' => 'decimal:2',
    ];

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function court(): BelongsTo
    {
        return $this->belongsTo(Court::class);
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
}
