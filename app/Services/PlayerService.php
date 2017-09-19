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

    public function eligiblePlayers () {
        return Player::has('plays','>=',10);
    }


    public function getPlayers(){
        $players = $this->eligiblePlayers()->with('plays')->get();
        $output = collect();
        foreach ($players as $player){
            $opponents = 0;
            $wins = 0;
            $play_count = 0;
            foreach ($player->plays as $play){
                $opponents_in_game =  $play->teams ?   $play->teams :  count($play->players);
                $opponents += $opponents_in_game;
                $wins += $play->pivot->place;
                $play_count += 1;
            }
            $player->wins = $wins;
            $player->opponents = $opponents;
            $player->play_count = $play_count;
            $player->expected_win_rate = 1/($opponents/$play_count);
            $player->win_rate = $wins/$player->play_count;
            $player->adjusted_win_rate = $player->win_rate/$player->expected_win_rate;
            $player->expected_wins = $player->expected_win_rate * $play_count;
            $player->woe = $player->wins - $player->expected_wins;
            $output->push($player);
        }
        return array_values($output->sortByDesc('adjusted_win_rate')->toArray());
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
}