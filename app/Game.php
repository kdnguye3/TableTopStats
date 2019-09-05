<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = [
        'name',
    ];

    public function plays()
    {
        return $this->hasMany(\App\Play::class);
    }
}
