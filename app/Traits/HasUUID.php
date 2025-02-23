<?php

namespace App\Traits;
use Illuminate\Support\Str;

trait HasUUID
{
    public static function boot() {
        parent::boot();
        static::creating(function ($model) {
            $model->uuid = Str::uuid();
        });
    }
}
