<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    static function failResponse($msg, int $code)
    {
        return response()->json(['status' => $code, 'error' => $msg]);
    }

    // static function successResponse($data)
    // {
    // 	return response()->json(['status' => Response::HTTP_OK, 'data' => $data]);
    // }

    static function defaultResponse($data, $code)
    {
    	return response()->json($data, $code);
    }

    static function successResponse($data)
    {
    	return response()->json(['data' => $data], Response::HTTP_OK);
    }
}
