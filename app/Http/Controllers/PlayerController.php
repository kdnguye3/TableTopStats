<?php

namespace App\Http\Controllers;
use App\Group;
use App\Services\PlayerService;
use Illuminate\Http\Request;
use App\Player;
use GuzzleHttp;

class PlayerController extends Controller
{
    private $playerService;

    public function __construct(PlayerService $playerService)
    {
        $this->playerService = $playerService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('players.index');
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
    public function show($id)
    {
        //
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



    public function json(Request $request)
    {
        $result = collect();
        $season = intval($request->season);
        //validate turn group/season to ints
        $group = $request->group ? Group::find($request->group) : null;
        $result['players'] = $this->playerService->getPlayers($group, $season);
        $result['groups'] = Group::all();
        $result['group'] = intval($request->group);
        $result['season'] = $season;
        return response()->json($result);
    }
}
