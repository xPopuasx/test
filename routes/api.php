<?php


use Illuminate\Http\Request;

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
Route::get('/backlog','TaskController@index');
Route::get('/backlog/{task}', 'TaskController@show');

Route::post('/tasks', 'TaskController@store');
Route::post('/estimate/task', 'EstimationController@update');
Route::post('/sprints', 'SprintController@store');
Route::post('/sprints/add-task', 'SprintController@addTask');
Route::post('/sprints/start', 'SprintController@Start');
Route::post('/tasks/close', 'TaskController@Close');


