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
        $games = collect();
        $bggIds = "";
        foreach ($json['games'] as $game){
            $games->push(collect(['id'=>$game['id'], 'name'=>$game['name']]));
            $bggIds = $bggIds .  $game['bggId'] .",";

        }
        substr($bggIds, 0, -1);
        $client = new GuzzleHttp\Client();
        $res = $client->request('POST', 'https://www.boardgamegeek.com/xmlapi2/thing', [
            'query' => [
                'type' => 'boardgame',
                'id' => $bggIds,
                'stats' => 1
            ]
        ]);

        $xml = simplexml_load_string($res->getBody());
        $jsonE = json_encode($xml);
        $array = json_decode($jsonE,TRUE);

        foreach ($games as $key=>$game){
            $weight= $array['item'][$key]['statistics']['ratings']['averageweight']['@attributes']['value'];
            Game::firstOrCreate(['id'=>$game['id'],'name'=>$game['name'],'weight' => $weight]);
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
