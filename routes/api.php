<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BarberController;

Route::get('/',function (){
   return[
        'name'=>'API da aplicação DevBarber',
        'author'=>'Aldivio Alves Lisboa'
   ];
});

Route::post('auth/login',[AuthController::class,'login']);
Route::post('auth/logout',[AuthController::class,'logout']);
Route::post('auth/refresh',[AuthController::class,'refresh']);
Route::post('user',[AuthController::class,'create']);

Route::get('user/{id}',[UserController::class,'show']);
Route::put('user/{id}',[UserController::class,'update']);
Route::post('user/{id}',[UserController::class,'destroy']);
Route::get('user/favorites',[UserController::class,'getFavorites']);
Route::post('user/favorite',[UserController::class,'addFavorite']);
Route::get('user/appointments',[UserController::class,'getAppointments']);


Route::get('barbers',[BarberController::class,'index']);
Route::get('barbers/{id}',[BarberController::class,'show']);
Route::post('barbers/{id}/appointment',[BarberController::class,'setAppointment']);

Route::get('search',[BarberController::class,'search']);

Route::get('unauthozired',[AuthController::class,'unauthorized'])->name('login');

Route::get('random',[BarberController::class,'createRandom']);


