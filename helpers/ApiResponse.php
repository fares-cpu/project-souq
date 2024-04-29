<?php

namespace Knightu\Helpers;

use Illuminate\Http\JsonResponse;

class ApiResponse{

    /**
     * this function returns a successful response with status code 200
     * @param mixed $data
     * @param string $message
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public static function success($data = null, $message = null){
        return response()->json([
            "message" => $message,
            "status" => 200,
            "data" => $data
        ]);
    }
    /**
     * this function returns a failed response with status code 400 (or customized) and message(errors)
     * 
     * @param array $errors
     * @param string $message
     * @param int $status
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public static function error($errors = null, $message = "something went wrong", $status = 400){
        return response()->json([
            "message" => $message,
            "status" => $status,
            "errors" => $errors
        ],$status);
    }

    /**
     * this is the returned response for a validation error
     * 
     * @param array $errors
     * 
     * @return JsonResponse
     */
    public static function validationError($errors){
        return response()->json([
            "message" => "Validation Failed",
            "status" => 422,
            "errors" => $errors
        ], 422);
    }
    /**
     * this is the returned response for a not found error
     * 
     * @param array $errors
     * 
     * @return JsonResponse
     */
    public static function notFound($errors){
        return response()->json([
            "message" => "Not Found",
            "status" => 404,
            "errors" => $errors
        ], 404);
    }

    public static function unauthorized($errors){
        return response()->json([
            "message" => "Unauthorized",
            "status"  => 403,
            "errors"  => $errors
        ], 403);
    }





}