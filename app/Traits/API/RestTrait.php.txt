<?php

namespace App\Traits\API\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

trait RestTrait
{
    /**
     * Determine if the request is a rest api call.
     *
     * @param Request $request
     * @return bool
     */
    protected function isRestCall(Request $request): bool
    {
        return $request->wantsJson() || strpos($request->getUri(), '/api/v') !== false;
    }

    /**
     * Return a json response.
     *
     * @param string|array $payload
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function jsonResponse($payload = '',
                                    int $statusCode = JsonResponse::HTTP_OK): JsonResponse
    {
        $payload = $payload ?: [];
        return response()->json(['data' => $payload], $statusCode);
    }
}
