<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Throwable;

class Setting extends Model
{
    public const THEMES = ['dark', 'light'];

    protected $fillable = [
        'key',
        'value',
    ];

    public static function get(string $key, ?string $default = null): ?string
    {
        try {
            $value = Cache::rememberForever("setting.{$key}", fn () => static::query()->where('key', $key)->value('value'));
        } catch (Throwable $e) {
            return $default;
        }

        return $value ?? $default;
    }

    public static function set(string $key, ?string $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => $value]);

        Cache::forget("setting.{$key}");
    }

    public static function theme(): string
    {
        $theme = static::get('theme', 'dark');

        return in_array($theme, self::THEMES, true) ? $theme : 'dark';
    }

    public static function logoUrl(): ?string
    {
        $path = static::get('logo_path');

        return $path ? Storage::disk(config('filesystems.media'))->url($path) : null;
    }
}
