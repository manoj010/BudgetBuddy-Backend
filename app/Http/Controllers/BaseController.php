<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class BaseController extends Controller
{
    protected function success($data, $message = '', $status = Response::HTTP_OK)
    {
        return response()->json([
            'status' => 'success',
            'data' => $data,
            'message' => $message
        ], $status);
    }

    protected function error($e, $status = Response::HTTP_INTERNAL_SERVER_ERROR)
    {
        return response()->json([
            'status' => 'error',
            'message' => 'An error occurred: ' . $e->getMessage(),
        ], $status);
    }
}
