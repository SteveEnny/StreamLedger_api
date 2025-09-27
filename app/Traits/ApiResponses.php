<?php

namespace App\Traits;

trait ApiResponses
{
     protected function ok($message, $data = '', $statusCode = 200) {
        return $this->success($message, $data, $statusCode);
    }
    protected function success($message,$data, $statusCode = 200) {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }
    protected function successWithoutData($message) {
        return response()->json([
            'status' => 'success',
            'message' => $message,
        ], 200);
    }
    protected function error($message, $statusCode) {
        return response()->json([
            'message' => $message,
            'status' => $statusCode,
        ], $statusCode);
    }
}