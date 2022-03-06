<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $count = DB::table('users')->count();
        
        if($count == 0) {
            User::factory()->create([
                'email' => "admin@test.com",
                'password' => bcrypt('password'),
            ]);
    
            User::factory(5)->create();
        }        
    }
}
