<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;

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
            'message' => 'An error occurred: ' . $e,
        ], $status);
    }

    protected function checkOrFindResource($resource, $id = null, $message = 'Resource not found', $status = Response::HTTP_NOT_FOUND)
    {
        $userId = auth()->id();
        if ($id) {
            $resource = $resource->where('created_by', $userId)->find($id);
            if (!$resource) {
                return response()->json([
                    'status' => 'error',
                    'message' => $message
                ], $status);
            }
        } else {
            $resources = $resource->where('created_by', $userId)->get();
            if ($resources->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $message
                ], $status);
            }
            return $resources;
        }
        return $resource;
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
}
