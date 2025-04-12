<?php

namespace Tests\Traits;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Testing\TestResponse;

trait TestPaginationTrait
{
    private function testPagination(TestResponse $response, string $basePath): void
    {
        $lastPage = $response->json('last_page');
        $currentPage = $response->json('current_page');
        $perPage = $response->json('per_page');

        for ($page = $currentPage; $page <= $lastPage; $page++) {
            $pageResponse = $this->getJson($basePath.'?page='.$page.'&per_page='.$perPage);

            $pageResponse->assertStatus(Response::HTTP_OK);
            $this->assertLessThanOrEqual($perPage, count($pageResponse->json('data')));
            $this->assertEquals($page, $pageResponse->json('current_page'));

        }

        $pageResponse = $this->getJson($basePath.'?page='.$page.'&per_page='.$perPage);
        $pageResponse->assertStatus(Response::HTTP_OK);
        $pageResponse->assertJsonCount(0, 'data');
    }
}
