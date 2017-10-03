<?php

namespace App\Services;

use App\Group;
use App\Play;
use App\Player;
use Carbon\Carbon;


class PlayerService
{
    public function getWins(Player $player)
    {
        $wins = 0;
        foreach ($player->plays as $play) {
            if (!$play->ignored) {
            }
        }
        return $wins;
    }


    public function getPlayers($group = null, $season = null)
    {
        $players = $this->filterPlays($group, $season);
        $output = collect();

        foreach ($players as $player) {
            $opponents = 0;
            $wins = 0;
            $play_count = 0;
            $new_play_count = 0;
            $new_wins = 0;
            $new_opponents = 0;
            foreach ($player->plays as $play) {
                $opponents_in_game = $play->teams ? $play->teams : count($play->players);
                $opponents += $opponents_in_game;
                $wins += $play->pivot->place;
                $play_count += 1;
                $new_opponents += $opponents_in_game * $play->game->weight;
                $new_play_count += $play->game->weight;
                $new_wins += $play->pivot->place * $play->game->weight;

            }
            if (count($player->plays)) {
                $player->wins = $wins;
                $player->opponents = $opponents;
                $player->play_count = $play_count;
                $player->expected_win_rate = 1 / ($opponents / $play_count);
                $player->win_rate = $wins / $player->play_count;
                $player->adjusted_win_rate = $player->win_rate / $player->expected_win_rate;
                $player->expected_wins = $player->expected_win_rate * $play_count;
                $player->woe = $player->wins - $player->expected_wins;
                $player->new_play_count = $new_play_count;
                $player->new_wins = $new_wins;
                $player->new_expected_win_rate = ($new_play_count / $play_count) / ($new_opponents / $new_play_count);
                $player->new_win_rate = $new_wins / $new_play_count;
                $player->new_adjusted_win_rate = $player->new_win_rate / $player->new_expected_win_rate;

                $output->push($player);
            }

        }
        return array_values($output->sortByDesc('new_adjusted_win_rate')->toArray());
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
            $startDate = Carbon::createFromFormat('Y-m-d H:i:s', '2016-08-01 00:00:01');
            $endDate = Carbon::createFromFormat('Y-m-d H:i:s', '2017-05-29 23:59:59');
        } else if ($season === 2) {
            $startDate = Carbon::createFromFormat('Y-m-d H:i:s', '2017-05-30 00:00:00');
            $endDate = Carbon::createFromFormat('Y-m-d H:i:s', '2018-05-29 23:59:59');
        }

            $players = $group ? Player::group($group)->with(['plays' => function ($query) use ($group_names, $startDate, $endDate) {
                return $startDate ? $query->where('play_date', '>', $startDate)->where('play_date', '<', $endDate)
                    ->whereHas('players', function ($query) use ($group_names) {
                        $query->whereIn('name', $group_names);
                    }, '>=', 3)
                    : $query->whereHas('players', function ($query) use ($group_names) {
                        $query->whereIn('name', $group_names);
                    }, '>=', 3);
            }])->get()
                : Player::with(['plays' => function ($query) use ($startDate, $endDate) {
                    return $startDate ? $query->where('play_date', '>', $startDate)->where('play_date', '<', $endDate) : $query;
                }])->get();

        return $players;
    }

    public function filterPlays2($group, $season, $player = null)
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
        if ($player !== null) {
            $plays = $player->plays();
            $plays = $startDate ? $plays->where('play_date', '>', $startDate)->where('play_date', '<', $endDate) : $plays;
            $plays = $group ? $plays->whereHas('players', function ($query) use ($group_names) {
                $query->whereIn('name', $group_names);
            }, '>=', 3)
                : $plays;

        }
        return $plays->get();
    }


    public function getPlays($player, $group = null, $season = null)
    {
        $group_names = null;
        $startDate = null;
        $endDate = null;

        $plays = $this->filterPlays2($group, $season, $player);

        $games = collect();
        foreach ($plays as $play) {
            //if game name is set add to it
            $game = $play->game;
            if (isset($games[$game->id])) {
                $games[$game->id]['play_count'] += 1;
                $games[$game->id]['wins'] += $play->pivot->place;
                $games[$game->id]['new_play_count'] += $game->weight;
                $games[$game->id]['new_wins'] +=  $play->pivot->place * $game->weight;
            } else {
                $games[$game->id] = collect(['name' => $game->name,
                    'play_count' => 1,
                    'wins' => $play->pivot->place,
                    'new_play_count' => $game->weight,
                    'new_wins' => $play->pivot->place * $game->weight
                ]);
            }
        }

        $games = $games->map(function ($game) {
                $game['win_rate'] = $game['wins']/$game['play_count'] ;
                $game['new_win_rate'] = $game['new_wins']/$game['new_play_count'];
            return $game;
        });
        return array_values($games->sortByDesc(function ($value) {
            return $value['play_count'];
        })->toArray());

    }

    public function getExpectedWinRate(Player $player)
    {
        $opponents = 0;

        foreach ($player->plays as $play) {
            if (!$play->ignored) {
                $opponents = $play->teams ? $opponents + $play->teams : $opponents + count($play->players);
            }
        }
        return 1 / ($opponents / count($player->plays));

    }
}