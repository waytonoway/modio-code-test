<?php

namespace Tests\Feature;

use Database\Seeders\GameSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tests\Traits\TestPaginationTrait;
use Tests\Traits\TestUserTrait;

class GameTest extends TestCase
{
    use RefreshDatabase, TestUserTrait, TestPaginationTrait;

    public function testBrowseSucceeds() : void
    {
        $this->seed(GameSeeder::class);

        $this->actingAsTestUser();

        $response = $this
            ->get('/games')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'name',
                        'user_id',
                        'created_at',
                        'updated_at',
                    ]
                ],
                'current_page',
                'from',
                'last_page',
                'per_page',
                'to',
                'total',
            ]);

        $this->testPagination($response, '/games');
    }

    public function testCreateSucceedsWhileAuthenticated() : void
    {
        $this->actingAsTestUser();

        $this
            ->postJson('/games', [
                'name' => 'Rogue Knight'
            ])
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'id',
                'name',
                'user_id',
                'created_at',
                'updated_at'
            ])
            ->assertJsonFragment([
                'name' => 'Rogue Knight'
            ]);
    }

    public function testReadSucceeds() : void
    {
        $this->actingAsTestUser();

        $response = $this->postJson('/games', [
            'name' => 'Rogue Knight'
        ]);

        $this
            ->getJson('/games/'.$response->json('id'))
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'id',
                'name',
                'user_id',
                'created_at',
                'updated_at'
            ])
            ->assertJsonFragment([
                'name' => 'Rogue Knight',
            ]);
    }


    public function testCreateFailsWhileUnauthenticated() : void
    {
        $this
            ->postJson('/games', [
                'name' => 'Rogue Knight'
            ])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testUpdateSucceedsWhileAuthenticated() : void
    {
        $this->actingAsTestUser();

        $response = $this->postJson('/games', [
            'name' => 'Rogue Knight'
        ]);


        $this
            ->putJson('/games/'.$response->json('id'), [
                'name' => 'Rogue Knight Remastered'
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'id',
                'name',
                'user_id',
                'created_at',
                'updated_at'
            ])
            ->assertJsonFragment([
                'name' => 'Rogue Knight Remastered'
            ]);
    }

    public function testUpdateFailsWhileUnauthenticated() : void
    {
        $this->actingAsTestUser();
        $response = $this->postJson('/games', [
            'name' => 'Rogue Knight'
        ]);

        $this->logout();

        $this
            ->putJson('/games/'.$response->json('id'), [
                'name' => 'Rogue Knight Remastered'
            ])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testDeleteSucceedsWhileAuthenticated() : void
    {
        $this->actingAsTestUser();
        $response = $this->postJson('/games', [
            'name' => 'Rogue Knight'
        ]);

        $this
            ->getJson('/games/'.$response->json('id'))
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'id',
                'name',
                'user_id',
                'created_at',
                'updated_at'
            ])
            ->assertJsonFragment([
                'name' => 'Rogue Knight'
            ]);

        $this
            ->deleteJson('/games/'.$response->json('id'))
            ->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testDeleteFailsWhileUnauthenticated() : void
    {
        $this->actingAsTestUser();
        $response = $this->postJson('/games', [
            'name' => 'Rogue Knight'
        ]);

        $this
            ->getJson('/games/'.$response->json('id'))
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'id',
                'name',
                'user_id'
            ])
            ->assertJsonFragment([
                'name' => 'Rogue Knight'
            ]);

        $this->logout();

        $this
            ->deleteJson('/games/'.$response->json('id'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
