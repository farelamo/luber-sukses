<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function handleSort($sortingBy, $filterable){

        if (empty($sortingBy)) return false;

        if (len($filterable) == 0) :
            $filterable = ["id", "title", "subtitle", "created_at", "updated_at"];
        endif;
        
        return in_array($sortingBy, $filterable);
    }

    public function returnCondition($condition, $errorCode, $message)
    {
        return response()->json([
            'success' => $condition,
            'message' => $message,
        ], $errorCode);
    }
}
