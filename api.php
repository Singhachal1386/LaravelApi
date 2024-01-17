<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::namespace('App\Http\Controllers')->group(function(){

    // GET API - Fetch one or  more  records
    Route::get('users/{id?}','APIController@getUsers');

    // secure GET API - Fetch one or  more  records
    Route::get('users-list','APIController@getUsersList');

    // Post API - Add single user
    Route::post('add-users','APIController@addUsers');
    
    // Register API - Register User with API Token
    Route::post('register-user','APIController@registerUser');

    // 
    Route::post('register-user-with-passport','APIController@registerUserWithPassport');

    // Login API
    Route::post('login-user','APIController@LoginUser');


    // Logout API and  delete API Token
    Route::post('logout-user','APIController@logoutUser');


    // POST API - Add multiple users
    Route::post('add-multiple-users','APIController@addMultipleUsers');

    // PUT API - Update one  or more  records 

    Route::put('update-user-details/{id}','APIController@updateUserDetails');

    // Patch API  - Update Single  Recordes
    Route::patch('update-user-name/{id}','APIController@updateUserName');

    // DELETE API with Single  use  with param

    Route::delete('delete-user/{id}','APIController@deleteUser');

    // Delete API Single User with json
    Route::delete('delete-user-with-json','APIController@deleteUserWithJson');

    // DELETE API - Delete multiple users with param

    Route::delete('delete-multiple-user/{id}','APIController@deleteMultipleUsers');

    // DELETE API - Delete Multiple user with json

    Route::delete('delete-multiple-user-with-json','APIController@deletemultipleuserwithjson');
});
    



