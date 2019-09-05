<?php

namespace App\Http\Controllers;

use App\Game;
use App\Group;
use Illuminate\Http\Request;
use App\Services\GameService;

class GameController extends Controller
{
    private $gameService;

    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('games.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        return view('games.show', ['id'=>$id, 'group'=>$request->group, 'season'=>$request->season]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    //show games /complexity

    public function json(Request $request)
    {
        $result = collect();
        $season = intval($request->season);
        //validate turn group/season to ints
        $group = $request->group ? Group::find($request->group) : null;
        $result['games'] = $this->gameService->getGames($group, $season);
        $result['groups'] = Group::all();
        $result['group'] = intval($request->group);
        $result['season'] = $season;

        return response()->json($result);
    }

    public function gamejson(Game $game, Request $request)
    {
        $result = collect();
        $season = intval($request->season);
        $group = $request->group ? Group::find($request->group) : null;
        $result['players'] = $this->gameService->getPlayers($game, $group, $season);
        $result['groups'] = Group::all();
        $result['group'] = intval($request->group);
        $result['season'] = $season;
        $result['game'] = $game;

        return response()->json($result);
    }
}
