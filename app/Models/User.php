<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'office_id',
        'name',
        'email',
        'password',
        'role',
        'phone',
        'avatar',
        'syndicate_card_image',
        'email_verified_at',
    ];

    protected $appends = [
        'avatar_url',
        'syndicate_card_image_url',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if (! $this->avatar) {
            return null;
        }

        if (str_starts_with($this->avatar, 'http://') || str_starts_with($this->avatar, 'https://')) {
            return $this->avatar;
        }

        return asset('storage/' . $this->avatar);
    }

    public function getSyndicateCardImageUrlAttribute(): ?string
    {
        if (! $this->syndicate_card_image) {
            return null;
        }

        if (str_starts_with($this->syndicate_card_image, 'http://') || str_starts_with($this->syndicate_card_image, 'https://')) {
            return $this->syndicate_card_image;
        }

        return asset('storage/' . $this->syndicate_card_image);
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function assignedHearings(): HasMany
    {
        return $this->hasMany(Hearing::class, 'assigned_lawyer_id');
    }
}

