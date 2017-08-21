<?php

use Illuminate\Database\Seeder;
use App\Player;

class PlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Player::firstOrCreate(['name'=>'Kevin']);
        Player::firstOrCreate(['name'=>'Kristie']);
        Player::firstOrCreate(['name'=>'Angie']);
        Player::firstOrCreate(['name'=>'Ramail']);
        Player::firstOrCreate(['name'=>'Jon']);
        Player::firstOrCreate(['name'=>'Sabiha']);
        Player::firstOrCreate(['name'=>'Libby']);
        Player::firstOrCreate(['name'=>'Sab']);
        Player::firstOrCreate(['name'=>'Cynthia']);
        Player::firstOrCreate(['name'=>'Arun']);
    }
}
