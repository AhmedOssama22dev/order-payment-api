<?php

namespace App\Responses;

class ApiResponse
{
    /**
     * Return a success response.
     *
     * @param  string  $message
     * @param  mixed  $data
     * @param  int  $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    public static function success(string $message, $data = null, int $statusCode = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }

    /**
     * Return an error response.
     *
     * @param  string  $message
     * @param  int  $statusCode
     * @param  mixed  $data
     * @return \Illuminate\Http\JsonResponse
     */
    public static function error(string $message, int $statusCode = 400, $data = null)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }
}
