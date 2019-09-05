<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Play extends Model
{
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('valid', function (Builder $builder) {
            $builder->where('ignored', 0);
        });
    }

    public function game()
    {
        return $this->belongsTo(\App\Game::class);
    }

    public function players()
    {
        return $this->belongsToMany(\App\Player::class)->withPivot('place');
    }
}
