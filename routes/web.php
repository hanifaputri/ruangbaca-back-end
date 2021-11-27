<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
 */

/*$router->get('/', function () use ($router) {
    return $router->app->version();
});*/

$router->get('/', ['uses' => 'AuthController@test','middleware'=>'auth:user']);

$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('/register', ['uses' => 'AuthController@register']);
    $router->post('/login', ['uses' => 'AuthController@login']);
});

$router->group(['prefix' => 'books'], function () use ($router) {
    $router->get('/', ['uses' => 'BookController@getAllBooks']);

    $router->get('/{bookId}', ['uses' => 'BookController@getBookById']);

//     $router->post('/', ['uses' => 'BookController@insert']);

//     $router->put('/{bookId}', ['uses' => 'BookController@update']);

//     $router->delete('/{bookId}', ['uses' => 'BookController@delete']);

//     $router->post('/restore', ['uses' => 'BookController@restore']);
});

$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->group(['prefix' => 'users'], function () use ($router) {
        $router->get('/{userId}', ['uses' => 'UserController@getUserById']);

        $router->put('/{userId}', ['uses' => 'UserController@updateUser']);

        $router->delete('/{userId}', ['uses' => 'UserController@destroy']);
    });

    $router->group(['prefix' => 'transactions'], function () use ($router) {
        $router->get('/', function () {
            // TODO: Routes this to the right controller
        });

        $router->get('/{transactionId}', function () {
            // TODO: Routes this to the right controller
        });
    });
});

$router->group(['middleware' => 'auth:admin'], function () use ($router) {
    $router->group(['prefix' => 'users'], function () use ($router) {
        $router->get('/', ['uses' => 'UserController@getUserAll']);
    });

    $router->group(['prefix' => 'books'], function () use ($router) {
        $router->post('/', ['uses' => 'BookController@insert']);

        $router->put('/{bookId}', ['uses' => 'BookController@update']);

        $router->delete('/{bookId}', ['uses' => 'BookController@delete']);
    });

//     $router->group(['prefix' => 'transactions'], function () use ($router) {
//         $router->put('/{transactionId}', function () {
//             // TODO: Routes this to the right controller
//         });
//     });
// });

// $router->group(['middleware' => 'auth:user'], function () use ($router) {
//     $router->group(['prefix' => 'transactions'], function () use ($router) {
//         $router->post('/', function () {
//             // TODO: Routes this to the right controller
//         });
//     });
});
