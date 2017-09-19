<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{

    protected $fillable = [
        'name'
    ];

    protected $hidden = ['plays','updated_at','created_at'];

    public function plays() {
        return $this->belongsToMany(\App\Play::class)->withPivot('place');
    }

    public function wins() {
        $wins = 0;
        foreach ($this->plays as $play){
            if (!$play->ignored){
                $wins += $play->pivot->place ? 1 : 0;
            }
        }
        return $wins;
    }

    public function scopeLeague($query)
    {
        return $query->whereIn('name', ['Alex','Roshal','Angie','Rishi','Chris Mayer','Kristie','Kevin','Sabiha','Ramail'] );
    }
}
