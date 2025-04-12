<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Contracts\ModControllerInterface;
use App\Models\Game;
use App\Models\Mod;
use App\Services\ModService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group Mods
 * APIs for managing game mods
 */
class ModController extends Controller implements ModControllerInterface
{
    private ModService $modService;

    public function __construct(ModService $modService)
    {
        $this->modService = $modService;
    }

    /**
     * @OA\Get(
     *     path="/games/{game_id}/mods",
     *     summary="Browse all mods for a game",
     *     tags={"Mods"},
     *     @OA\Parameter(
     *         name="game_id",
     *         in="path",
     *         required=true,
     *         description="The ID of the game",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of mods",
     *         @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="items", type="array", @OA\Items(ref="#/components/schemas/Mod")),
     *              @OA\Property(property="current_page", type="integer"),
     *              @OA\Property(property="last_page", type="integer"),
     *              @OA\Property(property="per_page", type="integer"),
     *              @OA\Property(property="total", type="integer")
     *          )
     *     )
     * )
     */
    public function browse(Request $request, Game $game): JsonResponse
    {
        $this->authorize('view', Mod::class);

        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $mods = $this->modService->getModsForGame($game, $perPage, $page);

        return response()->json($mods);
    }

    /**
     * @OA\Post(
     *     path="/games/{game_id}/mods",
     *     summary="Create a new mod for a game",
     *     tags={"Mods"},
     *     @OA\Parameter(
     *         name="game_id",
     *         in="path",
     *         required=true,
     *         description="The ID of the game",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Mod")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Mod created",
     *         @OA\JsonContent(ref="#/components/schemas/Mod")
     *     )
     * )
     */
    public function create(Request $request, Game $game): JsonResponse
    {
        $this->authorize('create', Mod::class);

        $validatedData = $request->validate([
            'name' => ['required', 'string']
        ]);

        $mod = $this->modService->create($request->user(), $game, $validatedData);

        return response()->json($mod, Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/games/{game_id}/mods/{id}",
     *     summary="Read a specific mod for a game",
     *     tags={"Mods"},
     *     @OA\Parameter(
     *         name="game_id",
     *         in="path",
     *         required=true,
     *         description="The ID of the game",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="The ID of the mod",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Mod data",
     *         @OA\JsonContent(ref="#/components/schemas/Mod")
     *     )
     * )
     */
    public function read(Request $request, Game $game, Mod $mod): JsonResponse
    {
        $this->authorize('view', Mod::class);

        return response()->json($mod);
    }

    /**
     * @OA\Put(
     *     path="/games/{game_id}/mods/{id}",
     *     summary="Update a mod for a game",
     *     tags={"Mods"},
     *     @OA\Parameter(
     *         name="game_id",
     *         in="path",
     *         required=true,
     *         description="The ID of the game",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="The ID of the mod",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Mod")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Mod updated",
     *         @OA\JsonContent(ref="#/components/schemas/Mod")
     *     )
     * )
     */
    public function update(Request $request, Game $game, Mod $mod): JsonResponse
    {
        $this->authorize('manage', $mod);

        $validated = $this->validateMod($request);

        $updated = $this->modService->update($mod, $validated);

        return response()->json($updated);
    }

    /**
     * @OA\Delete(
     *     path="/games/{game_id}/mods/{id}",
     *     summary="Delete a mod for a game",
     *     tags={"Mods"},
     *     @OA\Parameter(
     *         name="game_id",
     *         in="path",
     *         required=true,
     *         description="The ID of the game",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="The ID of the mod",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Mod deleted")
     * )
     */
    public function delete(Request $request, Game $game, Mod $mod): JsonResponse
    {
        $this->authorize('manage', $mod);

        $this->modService->delete($mod);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    private function validateMod(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);
    }
}
