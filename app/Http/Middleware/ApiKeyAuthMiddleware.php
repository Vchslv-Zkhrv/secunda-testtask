<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tiny middleware to check ApiKey authorization
 */
class ApiKeyAuthMiddleware
{
    /**
     * @param  Request                                                                           $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     *
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization', '');
        $token = base64_decode($token);
        if (empty($token)) {
            return new Response("No Authorization header provided", 401);
        }

        if (strlen($token) <= 36) {
            return new Response("Invalid Authorization token", 401);
        }

        $id = substr($token, 0, 36);
        $sign = substr($token, 36);

        if (md5($id . env('APP_KEY')) != $sign) {
            return new Response("Authorization token corrupted", 401);
        }

        $apiKey = ApiKey::query()->find($id);
        if ($apiKey === null) {
            return new Response("Unknown Authorization token", 401);
        }

        if ($apiKey->deleted_at !== null) {
            return new Response("Access restricted", 401);
        }

        if ($apiKey->valid_till !== null && $apiKey->valid_till <= new \DateTime('now')) {
            return new Response("Authorization token expired", 401);
        }

        return $next($request);
    }
}
