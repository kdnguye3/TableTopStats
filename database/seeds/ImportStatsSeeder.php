<?php

use Illuminate\Database\Seeder;
use App\Game;
use App\Player;
use App\Play;

class ImportStatsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = json_decode(file_get_contents('./database/seeds/BGStatsExport.json'),true);
        //dd($json['plays'][3]);
        foreach ($json['games'] as $game){
            Game::firstOrCreate(['id'=>$game['id'],'name'=>$game['name']]);
        }
        foreach ($json['players'] as $player){
            Player::firstOrCreate(['id'=>$player['id'],'name'=>$player['name']]);
        }
        foreach ($json['plays'] as $play){
            // if
           // dd($play['playDate']);

            if ($play['gameRefId'] === 20){
                $newPlay = Play::firstOrCreate(['uuid' => $play['uuid'],'play_date'=>$play['playDate'],'ignored'=>$play['ignored'],'game_id'=>$play['gameRefId'],'teams'=>  4 ]);
            }
            else{
                $newPlay = Play::firstOrCreate(['uuid' => $play['uuid'],'play_date'=>$play['playDate'],'ignored'=>$play['ignored'],'game_id'=>$play['gameRefId'],'teams'=> $play['usesTeams'] ? 2 : 0]);
            }
            foreach ($play['playerScores'] as $player){
                if ($play['gameRefId'] === 45 && $player['playerRefId'] === 23){
                    //dd($play['playerScores'], $player);
                }
                $newPlay->players()->attach($player['playerRefId'],['place'=> $player['winner']]);
            }

        }
    }
}
