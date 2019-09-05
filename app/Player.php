<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    protected $fillable = [
        'name',
    ];

    protected $hidden = ['plays', 'updated_at', 'created_at'];

    public function plays()
    {
        return $this->belongsToMany(\App\Play::class)->withPivot('place');
    }

    public function groups()
    {
        return $this->belongsToMany(\App\Group::class);
    }

    public function wins()
    {
        $wins = 0;
        foreach ($this->plays as $play) {
            if (! $play->ignored) {
                $wins += $play->pivot->place ? 1 : 0;
            }
        }

        return $wins;
    }

    //should take in group
    public function scopeGroup($query, Group $group)
    {
        return $query->whereHas('groups', function ($query) use ($group) {
            $query->where('groups.id', $group->id);
        });
    }
}
