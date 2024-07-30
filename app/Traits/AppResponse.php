<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

trait AppResponse
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
            'message' => 'An error occurred: ' . $e,
        ], $status);
    }
    
    protected function checkOwnership($resource, $message = 'Permission Denied.', $status = Response::HTTP_FORBIDDEN)
    {
        $user = auth()->user();
        if ($resource->created_by !== $user->id) {
            return response()->json([
                'status' => 'error',
                'message' => $message
            ], $status);
        }
        return null;
    }

    public function notFound($status = Response::HTTP_NOT_FOUND)
    {
        return response()->json([
            'status' => 'error', 
            'message' => 'Resource not Found'
        ], $status);
        return null;
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
            'errors' => $validator->errors()
        ], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
