<?php

function jsonResponse($data = [], $status = 200, $message = 'OK', $errors = [])
{
    return response()->json([
        'data' => $data,
        'status' => $status,
        'message' => $message,
        'errors' => $errors
    ], $status);
}