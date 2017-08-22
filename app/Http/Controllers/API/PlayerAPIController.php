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
        $result = [];
        foreach ($players as $player){
            $player = $this->playerService->publishStats($player);
            $player_array->push($player) ;
        }
        $array = $player_array->filter(function($player){
            return $player->play_count>=10;
        })->sortByDesc('adjusted_win_rate')->toArray();
        //dd($array);
        //dd($player_array);
        //dd($result);
        return response()->json((array_values($array)));
    }
    public function getLeaderBoard(Request $request){
        $data =  $request->json()->all();
        if ($data['session']['application']['applicationId'] === "amzn1.ask.skill.2a00a5c7-e5da-409d-aaa5-fd77513678a7"){
            //
            if ($data['request']['type'] === 'IntentRequest'){
                $response = [];
                $response['version'] = '1.0';
                $response['response'] = [];
                $response['response']['outputSpeech'] = [];
                $response['response']['outputSpeech']['type'] = 'PlainText';
                $response['response']['outputSpeech']['text'] = 'Kevin';
                return response()->json($response);


            }

        }
        else {
            return response()->json(['error'=> ["Forbidden"] ], 403);
        }
    }
}
