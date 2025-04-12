<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Contracts\GameControllerInterface;
use App\Models\Game;
use App\Services\GameService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @OA\Tag(
 *     name="Games",
 *     description="Game management endpoints"
 * )
 */
class GameController extends Controller implements GameControllerInterface
{
    private GameService $gameService;

    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    /**
     * @OA\Get(
     *     path="/games",
     *     summary="Browse all games",
     *     tags={"Games"},
     *     @OA\Response(
     *         response=200,
     *         description="List of games",
     *         @OA\JsonContent(
     *               type="object",
     *               @OA\Property(property="items", type="array", @OA\Items(ref="#/components/schemas/Game")),
     *               @OA\Property(property="current_page", type="integer"),
     *               @OA\Property(property="last_page", type="integer"),
     *               @OA\Property(property="per_page", type="integer"),
     *               @OA\Property(property="total", type="integer")
     *           )
     *     )
     * )
     */
    public function browse(Request $request): JsonResponse
    {
        $this->authorize('view', Game::class);

        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);

        $games = $this->gameService->getWithPagination($perPage, $page);

        return response()->json($games);
    }

    /**
     * @OA\Post(
     *     path="/games",
     *     summary="Create a new game",
     *     tags={"Games"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Game")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Game created",
     *         @OA\JsonContent(ref="#/components/schemas/Game")
     *     )
     * )
     */
    public function create(Request $request): JsonResponse
    {
        $this->authorize('create', Game::class);
        $validated = $this->validateGame($request);

        $game = $this->gameService->create($request->user(), $validated);

        return response()->json($game, Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/games/{id}",
     *     summary="Read a specific game",
     *     tags={"Games"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Game ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Game data",
     *         @OA\JsonContent(ref="#/components/schemas/Game")
     *     )
     * )
     */
    public function read(Request $request, Game $game): JsonResponse
    {
        $this->authorize('view', $game);

        return response()->json($game);
    }

    /**
     * @OA\Put(
     *     path="/games/{id}",
     *     summary="Update a game",
     *     tags={"Games"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Game ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Game")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Game updated",
     *         @OA\JsonContent(ref="#/components/schemas/Game")
     *     ),
     *     @OA\Response(response=403, description="Not authorized"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function update(Request $request, Game $game): JsonResponse
    {
        $this->authorize('manage', $game);
        $validated = $this->validateGame($request);

        $updated = $this->gameService->update($game, $validated);

        return response()->json($updated);
    }

    /**
     * @OA\Delete(
     *     path="/games/{id}",
     *     summary="Delete a game",
     *     tags={"Games"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Game ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Game deleted"),
     *     @OA\Response(response=403, description="Not authorized"),
     *     @OA\Response(response=404, description="Not found")
     * )
     */
    public function delete(Request $request, Game $game): JsonResponse
    {
        $this->authorize('manage', $game);

        $this->gameService->delete($game);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    private function validateGame(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);
    }
}
