<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        //$this->call(GameSeeder::class);
        //$this->call(PlayerSeeder::class);
        $this->call(ImportStatsSeeder::class);


    }
}
