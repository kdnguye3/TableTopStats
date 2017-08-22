<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Play;
use App\Services\PlayerService;

use Illuminate\Http\Request;
use App\Player;

class PlayerAPIController extends Controller
{
    private $playerService;

    public function __construct(PlayerService $playerService)
    {
        $this->playerService = $playerService;
    }

    public function index()
    {
        $players = Player::all();
        $player_array = collect();
        foreach ($players as $player){
            $player = $this->playerService->publishStats($player);
            $player_array->push($player) ;
        }
        $array = $player_array->filter(function($player){
            return $player->play_count>=10;
        })->sortByDesc('adjusted_win_rate')->toArray();
        return response()->json((array_values($array)));
    }

    public function getPlayers()
    {
        $players = Player::all();
        $player_array = collect();
        foreach ($players as $player){
            $player = $this->playerService->publishStats($player);
            $player_array->push($player) ;
        }
        return $player_array;
    }

    public function playersByActualWinRate()
    {
        $players = $this->getPlayers();
        $sorted_players = $players->filter(function($player){
            return $player->play_count>=10;
        })->sortByDesc('win_rate');
        return $sorted_players;
    }

    public function playersByAdjusted()
    {
        $players = $this->getPlayers();
        $sorted_players = $players->filter(function($player){
            return $player->play_count>=10;
        })->sortByDesc('adjusted_win_rate');
        return $sorted_players;
    }


    public function getResult(Request $request)
    {
        $data = $request->json()->all();
        if ($data['session']['application']['applicationId'] === "amzn1.ask.skill.2a00a5c7-e5da-409d-aaa5-fd77513678a7") {
            if ($data['request']['type'] === 'IntentRequest') {
                $intent = $data['request']['intent']['name'];
                switch ($intent){
                    case "GetLeaderBoard":
                        return $this->getLeaderBoard($data['request']['intent']['slots']);

                    case "GetPlayerInfo" :
                        return $this->getPlayerStats($data['request']['intent']['slots']);
                }
            }
        }
        else {
            return response()->json(['error'=> ["Forbidden"] ], 403);
        }
    }

    public function getPlayerStats($slots)
    {
        $name = substr($slots['player']['value'],0,-1);
        $name = preg_replace_callback('/([.!?])\s*(\w)/', function ($matches) {
            return strtoupper($matches[1] . ' ' . $matches[2]);
        }, ucfirst(strtolower($name)));
        $player = $this->playerService->publishStats(Player::where('name','=',$name)->first());
        $percent = round($player['win_rate']*100) . "%";
        $result_text = $name . " has won " . $player['wins'] . " games with a " . $percent . " win rate.";
        $response = [];
        $response['version'] = '1.0';
        $response['response'] = [];
        $response['response']['outputSpeech'] = [];
        $response['response']['outputSpeech']['type'] = 'PlainText';
        $response['response']['outputSpeech']['text'] = $result_text;
        return response()->json($response);

    }

    public function getLeaderBoard($slots)
    {
        $num_players = isset($slots['numLeaders']['value']) ? $slots['numLeaders']['value'] : 3;
        $players = array_values($this->playersByActualWinRate()->slice(0,$num_players)->toArray());
        $places = ['first','second','third','fourth', 'fifth', 'sixth','seventh','eighth','ninth','tenth'];
        $result_text = "";
        for ($i=$num_players-1;$i>=0;$i--){
            $percent = round($players[$i]['win_rate']*100) . "%";
            if (!$i){
                $result_text = $result_text . "And finally " . $players[$i]['name'] . " is in " . $places[$i] . " place with a " . $percent . " win rate." ;

            }
            else{
                $result_text = $result_text . $players[$i]['name'] . " is in " . $places[$i] . " place with a " . $percent . " win rate. " ;
            }
        }
        $response = [];
        $response['version'] = '1.0';
        $response['response'] = [];
        $response['response']['outputSpeech'] = [];
        $response['response']['outputSpeech']['type'] = 'PlainText';
        $response['response']['outputSpeech']['text'] = $result_text;

        return response()->json($response);
    }
}
