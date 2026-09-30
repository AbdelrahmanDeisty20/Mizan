<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Governorate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar',
        'name_en',
    ];

    public function offices(): HasMany
    {
        return $this->hasMany(Office::class);
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    public function serviceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class);
    }
    public function getNameAttribute(): ?string
    {
        return app()->getLocale() === 'en' 
            ? ($this->name_en ?? $this->name_ar) 
            : ($this->name_ar ?? $this->name_en);
    }
}
