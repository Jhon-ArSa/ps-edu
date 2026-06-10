<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, mixed $default = null): mixed
    {
        try {
            return Cache::rememberForever("setting_{$key}", function () use ($key, $default) {
                return static::where('key', $key)->value('value') ?? $default;
            });
        } catch (\Exception $e) {
            // Si falla la conexión a BD, retornar el valor por defecto
            \Log::warning("Setting::get() failed for key '{$key}': " . $e->getMessage());
            return $default;
        }
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("setting_{$key}");
    }
}
