<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    public function __construct(){
        $this->middleware('auth:api',[
            'except'=>['create','login']
        ]);
    }


    public  function create(Request  $request){
        $validator = Validator::make($request->all(),[
           'name'=>['required'],
           'email'=>['required','email'],
           'password'=>['required']
        ]);
        if($validator->fails()){
            return $this->sendErrorResponse('Erro ao cadastrar o usuário',$validator->errors());
        }
        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');

        $emailExists = User::where('email',$email)->first();
        if($emailExists){
            return $this->sendErrorResponse('Email já está cadastrado no sistema');
        }

        $user = new User();
        $user->email = $email;
        $user->name = $name;
        $user->password = password_hash($password,PASSWORD_DEFAULT);
        $user->save();
        $token = \auth()->attempt([
            'email'=>$email,
            'password'=>$password
        ]);
        if(!$token){
            return $this->sendErrorResponse('Ocorreu um erro no servidor.',500);
        }
        $user = \auth()->user();
        $avatar = url('media/avatars/'.$user['avatar']);
        $result = [
            'user'=>\auth()->user(),
            'avatar'=>$avatar,
            'token'=>$token
        ];
        return $this->sendResponse($result,'Usuário cadastrado com sucesso',201);
    }
}
