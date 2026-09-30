<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
    ];

    public function getImageUrlAttribute(): ?string
    {
        if ($this->type === 'image' && $this->value) {
            return asset('storage/settings/' . $this->value);
        }

        return null;
    }

    public static function getValue(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();

        if (! $setting) {
            return $default;
        }

        if ($setting->type === 'image' && $setting->value) {
            $decoded = json_decode($setting->value, true);

            if (is_array($decoded)) {
                return array_map(function ($path) {
                    return (new self)->getImageUrl($path);
                }, $decoded);
            }

            return (new self)->getImageUrl($setting->value);
        }

        if ($setting->type === 'json' || is_array(json_decode($setting->value, true))) {
            return json_decode($setting->value, true);
        }

        return $setting->value;
    }

    public static function setValue(string $key, $value, string $type = 'text')
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type]
        );
    }

    protected function getImageUrl($path): string
    {
        if (str_starts_with($path, 'http')) {
            return $path;
        }

        return asset('storage/settings/' . $path);
    }
}
