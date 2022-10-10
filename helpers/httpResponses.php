<?php

namespace Helpers;

class RES
{
    static public function OK($message = "Ok")
    {
        return response([
            "data" => $message,
            "success" => true,
            "status_code" => 200
        ], 200);
    }

    static public function CREATED($message = "Created")
    {
        return response([
            "data" => $message,
            "success" => true,
            "status_code" => 201
        ], 201);
    }

    static public function ACCEPTED($message = "Accepted")
    {
        return response([
            "data" => $message,
            "success" => true,
            "status_code" => 202
        ], 202);
    }

    static public function UNAUTHORIZED($message = "Unauthorized")
    {
        return response([
            "data" => $message,
            "success" => false,
            "status_code" => 401
        ], 401);
    }

    static public function NOTFOUND($message = "Not found")
    {
        return response([
            "data" => $message,
            "success" => false,
            "status_code" => 404
        ], 404);
    }
}
