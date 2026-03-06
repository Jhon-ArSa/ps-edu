<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlumnoProfile extends Model
{
    protected $fillable = ['user_id', 'code', 'promotion_year', 'program'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
