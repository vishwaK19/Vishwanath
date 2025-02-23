<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class UserPreference extends Model
{
    use HasApiTokens;
    
    protected $fillable = ['user_id','preferred_sources','preferred_categories','preferred_authors',];

    public function user() {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'preferred_sources' => 'array',
        'preferred_categories' => 'array',
        'preferred_authors' => 'array',
    ];
}
