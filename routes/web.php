<?php

use Illuminate\Support\Facades\Route;

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

Auth::routes(['verify' => true]);

Route::get('/', 'DashboardController')->name('dashboard');

Route::resource('/projects', 'ProjectController');
Route::get('/projects/{project}/languages/create', 'ProjectController@createProjectLanguage')
    ->name('projects.create-language');
Route::post('/projects/{project}/languages', 'ProjectController@storeProjectLanguage')
    ->name('projects.store-language');
Route::delete('/projects/{project}/languages/{language_code}', 'ProjectController@destroyProjectLanguage')
    ->name('projects.destroy-language');
Route::get('/projects/{project}/export', 'ProjectController@export')->name('projects.export');
Route::post('/projects/{project}/export', 'ProjectController@exportLanguage')->name('projects.export-language');

Route::resource('/projects/{project}/messages', 'MessageController')
    ->except(['show']);

Route::put('/projects/{project}/messages/{message}/{language_code}', 'MessageValueController@put')
    ->name('message-values.put');

Route::resource('/projects/{project}/members', 'ProjectMemberController', ['names' => 'project-members'])
    ->except(['show']);

Route::resource('/languages', 'LanguageController')
    ->except(['show']);

Route::resource('/users', 'UserController')
    ->except(['show']);

Route::get('/account/preferences', 'AccountController@preferences')->name('account.preferences');
Route::post('/account/preferences', 'AccountController@updatePreferences')->name('account.update-preferences');
Route::get('/account/change-password', 'AccountController@changePassword')->name('account.change-password');
Route::post('/account/change-password', 'AccountController@updatePassword')->name('account.update-password');
