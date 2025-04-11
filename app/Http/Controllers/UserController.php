<?php
namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="User",
 *     description="Operations related to user management"
 * )
 */
class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/user",
     *     summary="Get the authenticated user",
     *     description="Retrieve the details of the currently authenticated user",
     *     tags={"User"},
     *     @OA\Response(
     *         response=200,
     *         description="User retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function read(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->applyContext($request);

        return response()->json($user);
    }

    /**
     * @OA\Delete(
     *     path="/api/user",
     *     summary="Delete the authenticated user",
     *     description="Delete the currently authenticated user",
     *     tags={"User"},
     *     @OA\Response(
     *         response=200,
     *         description="User deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function delete(Request $request): JsonResponse
    {
        $user = $request->user();

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}

