<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\Mod;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class ModSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::factory(5)->create();
        $games = Game::factory(3)->create();

        Mod::factory(21)->state(new Sequence(
            fn ($sequence) => [
                'user_id' => $users->random()->id,
                'game_id' => $games->random()->id,
            ]
        ))->create();
    }
}
