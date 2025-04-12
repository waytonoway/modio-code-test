<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class GameTest extends TestCase
{
    use RefreshDatabase;
    public function testBrowseSucceeds() : void
    {
        Artisan::call('db:seed', ['--class' => 'GameSeeder']);

        $this->actingAsTestUser();

        $response = $this
            ->get('api/games')
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

        $lastPage = $response->json('last_page');
        $currentPage = $response->json('current_page');
        $perPage = $response->json('per_page');

        for ($page = $currentPage; $page <= $lastPage; $page++) {
            $pageResponse = $this->getJson('api/games?page='.$page.'&per_page='.$perPage);

            $pageResponse->assertStatus(Response::HTTP_OK);
        }
    }

    public function testCreateSucceedsWhileAuthenticated() : void
    {
        $this->actingAsTestUser();

        $this
            ->postJson('api/games', [
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

        $response = $this->postJson('api/games', [
            'name' => 'Rogue Knight'
        ]);

        $this
            ->getJson('api/games/'.$response->json('id'))
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
            ->postJson('api/games', [
                'name' => 'Rogue Knight'
            ])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testUpdateSucceedsWhileAuthenticated() : void
    {
        $this->actingAsTestUser();

        $response = $this->postJson('api/games', [
            'name' => 'Rogue Knight'
        ]);


        $this
            ->putJson('api/games/'.$response->json('id'), [
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
        $response = $this->postJson('api/games', [
            'name' => 'Rogue Knight'
        ]);

        $this->logout();

        $this
            ->putJson('api/games/'.$response->json('id'), [
                'name' => 'Rogue Knight Remastered'
            ])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testDeleteSucceedsWhileAuthenticated() : void
    {
        $this->actingAsTestUser();
        $response = $this->postJson('api/games', [
            'name' => 'Rogue Knight'
        ]);

        $this
            ->getJson('api/games/'.$response->json('id'))
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
            ->deleteJson('api/games/'.$response->json('id'))
            ->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testDeleteFailsWhileUnauthenticated() : void
    {
        $this->actingAsTestUser();
        $response = $this->postJson('api/games', [
            'name' => 'Rogue Knight'
        ]);

        $this
            ->getJson('api/games/'.$response->json('id'))
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
            ->deleteJson('api/games/'.$response->json('id'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    private function actingAsTestUser(): User
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        return $user;
    }

    private function logout(): void {
        $this->flushSession();

        $this->app['auth']->forgetGuards();
    }
}
