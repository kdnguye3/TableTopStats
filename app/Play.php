<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Play extends Model
{
    public function game(){
        return $this->belongsTo(\App\Game::class);
    }

    public function players(){
        return $this->belongsToMany(\App\Player::class)->withPivot('place');
    }
}
