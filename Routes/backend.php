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
        'prefix' => '/form/',
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
        'prefix' => 'backend/forms/content-forms',
        'namespace'  => 'Backend',
        'middleware' => ['web', 'has.backend.access'],
    ],
    function () {
        //---------------------------------------------------------
        Route::get('/assets', 'ContactFormController@getAssets')
            ->name('backend.cms.content.types.assets');
        //---------------------------------------------------------
        Route::post('/create', 'ContactFormController@postCreate')
            ->name('backend.cms.content.types.create');
        //---------------------------------------------------------
        Route::get('/list', 'ContactFormController@getList')
            ->name('backend.cms.content.types.list');
        //---------------------------------------------------------
        Route::get('/item/{id}', 'ContactFormController@getItem')
            ->name('backend.cms.content.types.item');
        //---------------------------------------------------------
        Route::post('/item/{id}/relations', 'ContactFormController@getItemRelations')
            ->name('backend.cms.content.types.item.relations');
        //---------------------------------------------------------
        Route::post('/store/{id}', 'ContactFormController@postStore')
            ->name('backend.cms.content.types.store');
        //---------------------------------------------------------
        Route::post('/store/{id}/groups', 'ContactFormController@postStoreGroups')
            ->name('backend.cms.content.types.store.groups');
        //---------------------------------------------------------
        Route::post('/actions/{action_name}', 'ContactFormController@postActions')
            ->name('backend.cms.content.types.actions');
        //---------------------------------------------------------
        Route::get('/getModuleSections', 'ContactFormController@getModuleSections')
            ->name('backend.cms.content.types.module-section');
        //---------------------------------------------------------
        Route::post('/upload', 'ContactFormController@upload')
            ->name('backend.cms.media.upload');
        //---------------------------------------------------------
    });

