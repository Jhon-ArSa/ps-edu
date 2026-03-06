<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocenteProfile extends Model
{
    protected $fillable = [
        'user_id', 'title', 'degree', 'specialty',
        'category', 'years_of_service', 'bio',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
