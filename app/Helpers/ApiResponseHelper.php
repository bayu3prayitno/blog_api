<?php

namespace App\Helpers;

class ApiResponseHelper
{
    /**
     * Format respon sukses.
     */
    public static function success($data, $message = 'Success', $code = 200)
    {
        return response()->json([
            'status'  => 'success',
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    /**
     * Format respon error.
     */
    public static function error($message = 'Error', $code = 400)
    {
        return response()->json([
            'status'  => 'error',
            'message' => $message,
        ], $code);
    }
}