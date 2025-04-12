<?php

namespace Tests\Traits;

use App\Models\User;

trait TestUserTrait
{
    /**
     * Create a user and authenticate them for testing.
     *
     * @return User
     */
    private function actingAsTestUser(): User
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        return $user;
    }

    /**
     * Logout the current authenticated user.
     *
     * @return void
     */
    private function logout(): void
    {
        $this->flushSession();

        $this->app['auth']->forgetGuards();
    }
}
