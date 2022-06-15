<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public  function sendResponse($data,$message,$code=200){
        return response()->json([
            'success'=>true,
            'message'=>$message,
            'data'=>$data
        ],$code);
    }
    public  function sendErrorResponse($message,$code=400,$data=[]){
        return response()->json([
            'success'=>false,
            'message'=>$message,
            'data'=>$data
        ],$code );
    }
}
