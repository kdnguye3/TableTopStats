<?php

namespace App\Services;
use App\Player;


class PlayerService
{
    public function getWins(Player $player){
        $wins = 0;
        foreach ($player->plays as $play){
            if (!$play->ignored){
            }
        }
        return $wins;
    }

    public function getExpectedWinRate(Player $player){
        $opponents = 0;

        foreach ($player->plays as $play){
            if (!$play->ignored){
                $opponents = $play->teams ? $opponents + $play->teams :  $opponents + count($play->players);
            }
        }
        return 1/($opponents/count($player->plays));

    }

    public function publishStats(Player $player){
        $opponents = 0;
        $wins = 0;
        $play_count = 0;
        $opponents_list = [];
        $sum2 = [];
        $sum3 = [];

        foreach ($player->plays as $play){
            if (!$play->ignored){
                if ($player->name === "Alex"){
                    $opponents_list[$play['id']] = $play->teams ?  $play->teams : count($play->players);
                }
                $opponents_in_game =  $play->teams ?   $play->teams :  count($play->players);
                if ($player->name === "Alex"){
                    $sum2[] = $opponents_in_game;
                    $sum3[$play['id']] = $opponents_in_game;

                }
                $opponents += $opponents_in_game;
                $wins += $play->pivot->place;
                $play_count += 1;
            }
        }
        $player->wins = $wins;
        $player->opponents = $opponents;
        $player->play_count = $play_count;
        $player->expected_win_rate = 1/($opponents/$play_count);
        $player->win_rate = $wins/$player->play_count;
        $player->adjusted_win_rate = $player->win_rate/$player->expected_win_rate;

        return $player;
    }
}