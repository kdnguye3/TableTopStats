<?php

use Illuminate\Database\Seeder;
use App\Group;
use App\Player;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $albany = Group::firstOrCreate(['name'=>'Albany League']);
        $la = Group::firstOrCreate(['name'=>'LA']);
        //get id
        $albany_players = Player::whereIn('name',['Kevin Nguyen','Kristie Taguma','Angie','Roshal','Ramail','Alex','Rishi','Chris Mayer','Sabiha'])->get()->pluck('id');
        $la_players = Player::whereIn('name',['Kevin Nguyen','Kristie Taguma','Jon Hao','Cynthia','Arun','Sab','Sean','Larry','Kameron Matsunami ','Allyson'])->get()->pluck('id');

        $albany->players()->attach($albany_players);
        $la->players()->attach($la_players);
    }
}
