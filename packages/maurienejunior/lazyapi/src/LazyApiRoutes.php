<?php

namespace maurienejunior\lazyapi;

use Illuminate\Support\Facades\Route;
class LazyApiRoutes{
    public static function basicApiWithPrefix($prefix, $controller){
        return Route::group(['prefix'=>$prefix], function() use ($controller){
            Route::get('/',[$controller, 'getAll']);
            Route::get('/{id}',[$controller, 'findOne']);
            Route::post('/',[$controller, 'store']);
            Route::post('/update',[$controller, 'updatePost']);
            Route::put('/',[$controller, 'update']);
            Route::delete('/{id}',[$controller, 'delete']);
        });
    }
}
