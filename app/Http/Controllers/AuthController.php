<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', [
            'except' => ['create', 'login', 'unauthorized']
        ]);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);
        if ($validator->fails()) {
            return $this->sendErrorResponse('Erro ao cadastrar o usuário', $validator->errors());
        }
        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');

        $emailExists = User::where('email', $email)->first();
        if ($emailExists) {
            return $this->sendErrorResponse('Email já está cadastrado no sistema');
        }

        $user = new User();
        $user->email = $email;
        $user->name = $name;
        $user->password = password_hash($password, PASSWORD_DEFAULT);
        $user->save();
        $token = \auth()->attempt([
            'email' => $email,
            'password' => $password
        ]);
        if (!$token) {
            return $this->sendErrorResponse('Ocorreu um erro no servidor.', 500);
        }
        $user = \auth()->user();
        $user['avatar'] = url('media/avatars/' . $user['avatar']);
        $result = [
            'user' => $user,
            'token' => $token
        ];
        return $this->sendResponse($result, 'Usuário cadastrado com sucesso', 201);
    }

    public function login(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');
        $token = auth()->attempt([
            'email' => $email,
            'password' => $password
        ]);
        if (!$token) {
            return $this->sendErrorResponse('Usuário e/ou senha inválidos');
        }
        $info = auth()->user();
        $info['avatar'] = url('media/avatars/' . $info['avatar']);
        $result = [
            'user' => $info,
            'token' => $token
        ];
        return $this->sendResponse($result, 'usuário logado com sucesso');
    }

    public function logout(Request $request)
    {
        \auth()->logout();
        return $this->sendResponse([], 'usuário deslogado com sucesso');
    }

    public function refresh(Request $request)
    {
        $token = \auth()->refresh();
        $info = auth()->user();
        $info['avatar'] = url('media/avatars/' . $info['avatar']);
        $result = [
            'user' => $info,
            'token' => $token
        ];
        return $this->sendResponse($result, 'Refresh realizado com sucesso!');
    }

    public function unauthorized()
    {
        return $this->sendErrorResponse('Usuário não autoziado', 401);
    }
}
