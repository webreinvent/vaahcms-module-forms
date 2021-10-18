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

Route::group(
    [
        'prefix'     => 'backend/forms',
        'middleware' => ['web', 'has.backend.access'],
        'namespace' => 'Backend',
    ],
    function () {
        //------------------------------------------------
        Route::get( '/', 'BackendController@index' )
            ->name( 'vh.backend.forms' );
        //------------------------------------------------
        //------------------------------------------------
        Route::post( '/assets', 'BackendController@getAssets' )
            ->name( 'vh.backend.forms.assets' );
        //------------------------------------------------
    });

Route::group(
    [
        'prefix' => '/forms/',
        'middleware' => ['web'],
        'namespace' => 'Backend',
    ],
    function () {
        //------------------------------------------------
        Route::post( '/submit', 'BackendController@formSubmit' )
            ->name( 'vh.backend.forms.submit' );
        //------------------------------------------------
        //------------------------------------------------
    });





//------------------------------------------------


Route::group(
    [
        'prefix' => 'backend/forms/content',
        'namespace'  => 'Backend',
        'middleware' => ['web', 'has.backend.access'],
    ],
    function () {
        //---------------------------------------------------------
        Route::get('/assets', 'ContactFormController@getAssets')
            ->name('backend.form.content.assets');
        //---------------------------------------------------------
        Route::post('/create', 'ContactFormController@postCreate')
            ->name('backend.form.content.create');
        //---------------------------------------------------------
        Route::get('/list', 'ContactFormController@getList')
            ->name('backend.form.content.list');
        //---------------------------------------------------------
        Route::get('/item/{id}', 'ContactFormController@getItem')
            ->name('backend.form.content.item');
        //---------------------------------------------------------
        Route::post('/item/{id}/relations', 'ContactFormController@getItemRelations')
            ->name('backend.form.content.item.relations');
        //---------------------------------------------------------
        Route::post('/store/{id}', 'ContactFormController@postStore')
            ->name('backend.form.content.store');
        //---------------------------------------------------------
        Route::post('/store/{id}/groups', 'ContactFormController@postStoreGroups')
            ->name('backend.form.content.store.groups');
        //---------------------------------------------------------
        Route::post('/actions/{action_name}', 'ContactFormController@postActions')
            ->name('backend.form.content.actions');
        //---------------------------------------------------------
        Route::get('/getModuleSections', 'ContactFormController@getModuleSections')
            ->name('backend.form.content.module-section');
        //---------------------------------------------------------
        Route::post('/upload', 'ContactFormController@upload')
            ->name('backend.form.media.upload');
        //---------------------------------------------------------
    });

