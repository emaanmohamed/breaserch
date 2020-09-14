<?php

namespace App\Traits;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

trait ApiTrait
{

    public function ApiResponseMessage($message, $code)
    {
        return response()->json([
            'code'    => $code,
            'message' => $message
        ], $code);
    }

    public function ApiResponseData($data , $code , $pagination = "")
    {
        return response()->json([
            'code' => $code,
            'data' => $data,
            'pagination' => $pagination
        ], $code);
    }



}
