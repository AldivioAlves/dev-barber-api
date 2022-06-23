<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    private $loggedUser;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->loggedUser = auth()->user();
    }

    public function  show(){
        $result=[];
        $user = $this->loggedUser;
        $user['avatar'] = url('media/avatars/'.$user['avatar']);
        $result['user']= $user;
        return $this->sendResponse($result,"Usuário retornado com sucesso");
    }
}
