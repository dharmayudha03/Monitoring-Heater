<?php

namespace App\Helpers;

class ApiResponse
{
    public static function success($data = null, $message = 'Success')
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ]);
    }

    public static function error($message = 'Error', $code = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], $code);
    }
}