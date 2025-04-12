<?php

namespace App\Http\Middleware;

use App\Models\Mod;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Response;

class ModBelongsToGame extends Middleware
{
    public function handle($request, \Closure $next, ...$guards){
        if (! $modId = $request->route()->parameter('mod')) {
            return $next($request);
        } elseif (! $gameId = $request->route()->parameter('game')) {
            return $next($request);
        }

        if (! Mod::where('id', $modId)->where('game_id', $gameId)->exists()) {
            return response()->json(['error' => 'Mod not found for this game'], Response::HTTP_NOT_FOUND);
        }

        return $next($request);
    }
}
