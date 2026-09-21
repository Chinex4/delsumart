<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function boolean(string $key, bool $default = false): bool
    {
        $value = static::query()->where('key', $key)->value('value');

        return $value === null ? $default : filter_var($value, FILTER_VALIDATE_BOOL);
    }

    public static function putBoolean(string $key, bool $value): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value ? '1' : '0'],
        );
    }
}
