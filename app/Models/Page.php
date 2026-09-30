<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'title_ar',
        'title_en',
        'content_ar',
        'content_en',
        'sections',
    ];

    protected $appends = ['title', 'content'];

    protected $hidden = ['title_ar', 'title_en', 'content_ar', 'content_en'];

    protected $casts = [
        'sections' => 'array',
    ];

    public function getTitleAttribute(): ?string
    {
        $locale = app()->getLocale();

        return $this->{"title_{$locale}"} ?? $this->title_ar ?? $this->title_en;
    }

    public function getContentAttribute(): ?string
    {
        $locale = app()->getLocale();

        return $this->{"content_{$locale}"} ?? $this->content_ar ?? $this->content_en;
    }
}
