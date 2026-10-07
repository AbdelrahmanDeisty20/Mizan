<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'office_id',
        'client_id',
        'legal_case_id',
        'title',
        'file_path',
        'file_type',
        'file_size',
        'extracted_text',
    ];

    protected $appends = [
        'file_url',
    ];

    public function getFileUrlAttribute(): ?string
    {
        if (! $this->file_path) {
            return null;
        }

        if (str_starts_with($this->file_path, 'http://') || str_starts_with($this->file_path, 'https://')) {
            return $this->file_path;
        }

        return asset('storage/' . $this->file_path);
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
}
