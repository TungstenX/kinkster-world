<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
// use App\Http\Controllers\SocialMediaController;
// Route::get('/social', [SocialMediaController::class, 'index'])->name('social.index');
// Route::post('/social/create-post', [SocialMediaController::class, 'createPost'])->name('social.createPost');
// Add routes for likes, comments, and followers here.

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



Route::group(['middleware' => ['web']], function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('home');

    Route::post('/register', [
        'uses' => 'App\Http\Controllers\UserController@postRegister',
        'as' => 'register'
    ]);

    Route::post('/signin', [
        'uses' => 'App\Http\Controllers\UserController@postSignIn',
        'as' => 'signin'
    ]);

    Route::get('/logout', [
        'uses' => 'App\Http\Controllers\UserController@getLogout',
        'as' => 'logout'
    ]);

  Route::get('/account', [
    'uses' => 'App\Http\Controllers\UserController@getAccount',
    'as' => 'account',
    'middleware' => 'auth'
  ]);

  Route::post('/upateaccount', [
    'uses' => 'App\Http\Controllers\UserController@postSaveAccount',
    'as' => 'account.save',
    'middleware' => 'auth'
  ]);

  Route::get('/userimage/{filename}', [
    'uses' => 'App\Http\Controllers\UserController@getUserImage',
    'as' => 'account.image'
  ]);

    Route::get('/dashboard', [
        'uses' => 'App\Http\Controllers\PostController@getDashboard',
        'as' => 'dashboard',
        'middleware' => 'auth'
    ]);

    Route::post('/createpost', [
        'uses' => 'App\Http\Controllers\PostController@postCreatePost',
        'as' => 'post.create',
        'middleware' => 'auth'
    ]);

    Route::get('/delete-post/{post_id}', [
        'uses' => 'App\Http\Controllers\PostController@getDeletePost',
        'as' => 'post.delete',
        'middleware' => 'auth'
    ]);

    Route::post('/edit', [
        'uses' => 'App\Http\Controllers\PostController@postEditPost',
        'as' => 'edit'
    ]);

    Route::post('/like', [
        'uses' => 'App\Http\Controllers\PostController@postLikePost',
        'as' => 'like'
    ]);

    Route::get('/post-profile/{post_id}', [
        'uses' => 'App\Http\Controllers\ImageController@getPostProfile',
        'as' => 'post.profile'
    ]);

  Route::get('/friends', [
    'uses' => 'App\Http\Controllers\FriendController@getFriends',
    'as' => 'friends',
    'middleware' => 'auth'
  ]);

  Route::get('/profilepic/{user_id}', [
    'uses' => 'App\Http\Controllers\ImageController@getUserProfilePic',
    'as' => 'image.profile'
  ]);

  Route::post('/friend/request', [
    'uses' => 'App\Http\Controllers\FriendController@postRequest',
    'as' => 'post.request',
    'middleware' => 'auth'
  ]);

  Route::post('/friend/cancel', [
    'uses' => 'App\Http\Controllers\FriendController@cancelRequest',
    'as' => 'cancel.request',
    'middleware' => 'auth'
  ]);
});
