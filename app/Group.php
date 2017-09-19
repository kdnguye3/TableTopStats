<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = ['name'];

    public function players() {
        return $this->belongsToMany(\App\Player::class);
    }
}
