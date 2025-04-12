<?php

namespace Tests\Feature;

use Database\Seeders\GameSeeder;
use Database\Seeders\ModSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Game;
use App\Models\Mod;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use Tests\Traits\TestPaginationTrait;
use Tests\Traits\TestUserTrait;

class ModTest extends TestCase
{
    use RefreshDatabase, TestUserTrait, TestPaginationTrait;

    public function testBrowseSucceeds() : void
    {
        $this->seed(ModSeeder::class);

        $user = $this->actingAsTestUser();
        $game = Game::inRandomOrder()->first();

        $mod = Mod::query()->where('game_id', '=', $game->id)->first();

        $response = $this
            ->getJson('/games/' . $game->id . '/mods')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'name',
                        'game_id',
                        'user_id',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'current_page',
                'from',
                'last_page',
                'per_page',
                'to',
                'total',
            ])
            ->assertJsonFragment([
                'id' => $mod->id,
                'game_id' => $mod->game_id,
                'user_id' => $mod->user_id
            ]);

        $this->testPagination($response, '/games/' . $game->id . '/mods');
    }

    public function testCreateSucceedsWhileAuthenticated() : void
    {
        $this->seed(ModSeeder::class);
        $game = Game::inRandomOrder()->first();

        $user = $this->actingAsTestUser();

        $name = 'Lightsaber';
        $this
            ->postJson('/games/' . $game->id . '/mods', [
                'name' => $name
            ])
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'id',
                'name',
                'game_id',
                'user_id',
                'created_at',
                'updated_at'
            ])
            ->assertJsonFragment([
                'name' => $name,
                'game_id' => $game->id,
                'user_id' => $user->id
            ]);
    }

    public function testReadSucceeds() : void
    {
        $user = $this->actingAsTestUser();

        $gameId = $this->createGame();

        $modName = 'Lightsaber';
        $modId = $this->createMod($gameId, $modName);

        $this
            ->get('/games/' . $gameId . '/mods/' . $modId)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'id',
                'name',
                'game_id',
                'user_id',
                'created_at',
                'updated_at'
            ])
            ->assertJsonFragment([
                'name' => $modName,
                'game_id' => $gameId,
                'user_id' => $user->id
            ]);
    }

    public function testCreateFailsWhileUnauthenticated() : void
    {
        $this->seed(GameSeeder::class);
        $game = Game::inRandomOrder()->first();

        $this
            ->postJson('games/' . $game->id . '/mods', [
                'name' => 'Lightsaber'
            ])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testUpdateSucceedsWhileAuthenticated() : void
    {
        $user = $this->actingAsTestUser();

        $gameId = $this->createGame();
        $modId = $this->createMod($gameId,'Lightsaber');

        $newName = 'Lightsabers (Full set)';
        $this
            ->putJson('games/' . $gameId . '/mods/' . $modId, [
                'name' => $newName
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'id',
                'name',
                'game_id',
                'user_id',
                'created_at',
                'updated_at'
            ])
            ->assertJsonFragment([
                'name' => $newName,
                'game_id' => $gameId,
                'user_id' => $user->id
            ]);
    }

    public function testUpdateFailsWhileUnauthenticated() : void
    {
        $this->actingAsTestUser();

        $gameId = $this->createGame();
        $modId = $this->createMod($gameId,'Lightsaber');
        $this->checkModExists($modId, $gameId);

        $this->logout();

        $this
            ->putJson('/games/' . $gameId . '/mods/' . $modId, [
                'name' => 'Lightsabers (Full set)'
            ])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testDeleteSucceedsWhileAuthenticated() : void
    {
        $this->actingAsTestUser();

        $gameId = $this->createGame();
        $modId = $this->createMod($gameId, 'Lightsaber');
        $this->checkModExists($modId, $gameId);

        $this
            ->deleteJson('games/' . $gameId . '/mods/' . $modId)
            ->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testDeleteFailsWhileUnauthenticated() : void
    {
        $this->actingAsTestUser();
        $gameId = $this->createGame();

        $modId = $this->createMod($gameId, 'Lightsaber');
        $this->checkModExists($modId, $gameId);

        $this->logout();

        $this
            ->deleteJson('/games/' . $gameId . '/mods/' . $modId)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    private function createGame(): int {
        return $this->postJson('/games', ['name' => 'Rogue Knight'])
            ->assertStatus(Response::HTTP_CREATED)
            ->json('id');
    }

    private function createMod(int $gameId, string $name): int
    {
        return $this->post('/games/' . $gameId . '/mods', ['name' => $name])
            ->assertStatus(Response::HTTP_CREATED)
            ->json('id');
    }

    private function checkModExists(int $modId, int $gameId)
    {
        return $this
            ->getJson('/games/' . $gameId . '/mods/' . $modId)
            ->assertStatus(Response::HTTP_OK);
    }
}
