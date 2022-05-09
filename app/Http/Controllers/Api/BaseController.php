<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BaseController extends Controller
{
    public function sendResponse($result , $message)
    {
        $response = [
            'success' => true,
            'message'=> $message,
            'data' => $result
        ];

        return response()->json($response,200);
    }

    public function sendError($error , $errorMessage =[] , $code = 404)
    {

        $response = [
            'success' => false,
            'message'=> $error
        ];
        if(!empty($errorMessage))
        {
            $response['data'] = $errorMessage;
        }
        else
            $response['data'] = null;

        return response()->json($response,$code);

    }
}
