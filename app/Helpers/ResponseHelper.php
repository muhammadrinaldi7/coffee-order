<?php

namespace App\Helpers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\JsonResponse;

class ResponseHelper
{
    public static function success(string $message, $data = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data instanceof JsonResource ? $data->resolve() : $data
        ], $code);
    }

    public static function error(string $message, int $code = 400): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message
        ], $code);
    }
}
