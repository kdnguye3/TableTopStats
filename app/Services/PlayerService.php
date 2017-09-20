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
        //get players in group
        //filter out plays that dont contain at least 3 members of the group
        $group_names = null;
        $startDate = null;
        $endDate = null;

        if ($group){
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
            : Player::with('plays')->get();
        $output = collect();
        foreach ($players as $player) {
            $opponents = 0;
            $wins = 0;
            $play_count = 0;
            foreach ($player->plays as $play) {
                $opponents_in_game = $play->teams ? $play->teams : count($play->players);
                $opponents += $opponents_in_game;
                $wins += $play->pivot->place;
                $play_count += 1;
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
                $output->push($player);
            }

        }
        return array_values($output->sortByDesc('adjusted_win_rate')->toArray());
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