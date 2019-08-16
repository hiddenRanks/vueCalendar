<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'MainController@index');

Route::get('/data', 'MainController@getData')->middleware('login');
Route::get('/check', 'UserController@loginStatus');

Route::post('/login', 'UserController@login');
Route::get('/logout', 'UserController@logout')->middleware('login'); //로그인한 사용자만 접근가능하게


Route::get('/todo', 'TodoController@getTodoList')->middleware('login');
Route::post('/todo', 'TodoController@postTodo')->middleware('login'); //로그인한 사용자만 일정등록 가능

Route::post('/delete', 'TodoController@deleteTodo')->middleware('login');
Route::post('/complete', 'TodoController@complTodo')->middleware('login');

Route::post('/searchDay', 'TodoController@calendarList')->middleware('login');
