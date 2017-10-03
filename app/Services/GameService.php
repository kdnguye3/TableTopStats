<?php

namespace App\Services;

use App\Game;
use App\Player;

use Carbon\Carbon;


class GameService
{


    public function getGames($group = null, $season = null)
    {
        $games = $this->filterPlays($group, $season);
        $output = collect();

        foreach ($games as $game) {
            if (count($game->plays)) {
                $game->play_count = count($game->plays);
                $output->push($game);
            }

        }
        return array_values($output->sortByDesc('play_count')->toArray());
    }

    public function filterPlays($group, $season)
    {
        $group_names = null;
        $startDate = null;
        $endDate = null;

        if ($group) {
            $group_names = $group->players()->pluck('name');
        }
        if ($season === 1) {
            //TODO create enum for this
            $startDate = Carbon::createFromFormat('Y-m-d H:i:s', '2016-08-01 00:00:01');
            $endDate = Carbon::createFromFormat('Y-m-d H:i:s', '2017-05-29 23:59:59');
        } else if ($season === 2) {
            $startDate = Carbon::createFromFormat('Y-m-d H:i:s', '2017-05-30 00:00:00');
            $endDate = Carbon::createFromFormat('Y-m-d H:i:s', '2018-05-29 23:59:59');
        }

        $games = $group ? Game::with(['plays' => function ($query) use ($group_names, $startDate, $endDate) {
            return $startDate ? $query->where('play_date', '>', $startDate)->where('play_date', '<', $endDate)
                ->whereHas('players', function ($query) use ($group_names) {
                    $query->whereIn('name', $group_names);
                }, '>=', 3)
                : $query->whereHas('players', function ($query) use ($group_names) {
                    $query->whereIn('name', $group_names);
                }, '>=', 3);
        }])->get() :
            Game::with(['plays' => function ($query) use ($startDate, $endDate) {
                return $startDate ? $query->where('play_date', '>', $startDate)->where('play_date', '<', $endDate) : $query;
            }])->get();

        return $games;
    }

    public function filterPlays2($group, $season, $game = null)
    {
        $group_names = null;
        $startDate = null;
        $endDate = null;
        //dd($group,$season,$player);
        if ($group) {
            $group_names = $group->players()->pluck('name');
        }
        if ($season === 1) {
            $startDate = Carbon::createFromFormat('Y-m-d H:i:s', '2016-08-01 00:00:01');
            $endDate = Carbon::createFromFormat('Y-m-d H:i:s', '2017-05-29 23:59:59');
        } else if ($season === 2) {
            $startDate = Carbon::createFromFormat('Y-m-d H:i:s', '2017-05-30 00:00:00');
            $endDate = Carbon::createFromFormat('Y-m-d H:i:s', '2018-05-29 23:59:59');
        }
        $plays = null;
        //dd("got here");

        if ($game !== null) {
            $plays = $game->plays();
            $plays = $startDate ? $plays->where('play_date', '>', $startDate)->where('play_date', '<', $endDate) : $plays;
            $plays = $group ? $plays->whereHas('players', function ($query) use ($group_names) {
                $query->whereIn('name', $group_names);
            }, '>=', 3)
                : $plays;

        }
        return $plays->get();
    }


    public function getPlayers($game, $group = null, $season = null)
    {
        $group_names = null;
        $startDate = null;
        $endDate = null;

        $plays = $this->filterPlays2($group, $season, $game);

        $output = collect();
        foreach ($plays as $play) {
            //if game name is set add to it
            //get players in play
            $players = $play->players;
            foreach ($players as $player){
                if (isset($output[$player->id])) {
                    $output[$player->id]['play_count'] += 1;
                    $output[$player->id]['wins'] += $player->pivot->place;
                } else {
                    $output[$player->id] = collect(['name' => $player->name,
                        'play_count' => 1,
                        'wins' => $player->pivot->place
                    ]);
                }

            }

        }


        $output = $output->map(function ($player) {
            $player['win_rate'] = $player['wins']/$player['play_count'] ;
            return $player;
        });

        return array_values($output->sortByDesc(function ($value) {
            return $value['win_rate'];
        })->toArray());

    }

}