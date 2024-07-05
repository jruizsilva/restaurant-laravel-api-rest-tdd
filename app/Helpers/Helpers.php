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

function transactional(callable $callback)
{
    DB::beginTransaction();
    try {
        $result = $callback();
        DB::commit();
        return $result;
    } catch (Exception $e) {
        DB::rollBack();
        throw $e;
    }
}