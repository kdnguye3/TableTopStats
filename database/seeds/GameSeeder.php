<?php

use App\Game;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Game::firstOrCreate(['name'=>'Kemet']);
        Game::firstOrCreate(['name'=>'El Grande']);
        Game::firstOrCreate(['name'=>'Deception: Murder In Hong Kong']);
        Game::firstOrCreate(['name'=>'Concordia']);
        Game::firstOrCreate(['name'=>'Aeons End']);
    }
}
