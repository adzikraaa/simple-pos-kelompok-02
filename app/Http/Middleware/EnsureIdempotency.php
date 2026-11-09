<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\IdempotencyKey;

class EnsureIdempotency
{
    public function handle(Request $request, Closure $next)
    {
        $key = $request->header('Idempotency-Key');

        if (!$key) {
            return response()->json(['message' => 'Idempotency-Key header missing'], 400);
        }

        $idempotency = IdempotencyKey::where('key', $key)->first();

        if ($idempotency) {
            if ($idempotency->request_body !== $request->all()) {
                return response()->json(['message' => 'Payload differs for this Idempotency-Key'], 422);
            }
            
            if (is_null($idempotency->response_status)) {
                return response()->json(['message' => 'Request is currently being processed'], 409);
            }

            return response()->json($idempotency->response_body, $idempotency->response_status)
                             ->header('Idempotent-Replayed', 'true');
        }

        $idempotency = IdempotencyKey::create([
            'key' => $key,
            'request_path' => $request->path(),
            'request_body' => $request->all(),
        ]);

        $response = $next($request);

        if ($response->isSuccessful()) {
            $idempotency->update([
                'response_status' => $response->status(),
                'response_body' => json_decode($response->getContent(), true),
            ]);
        } else {
            $idempotency->delete();
        }

        return $response;
    }
}