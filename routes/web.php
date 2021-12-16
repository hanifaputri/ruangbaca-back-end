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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

/*
|--------------------------------------------------------------------------
| Authenication
|--------------------------------------------------------------------------
|*/
$router->group(['prefix' => 'auth'], function () use ($router) {
    $router->post('/register', ['uses' => 'AuthController@register']);
    $router->post('/login', ['uses' => 'AuthController@login']);
});

/*
|--------------------------------------------------------------------------
| User
|--------------------------------------------------------------------------
|*/

$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->group(['prefix' => 'users'], function () use ($router) {
        $router->get('/{id}', ['uses' => 'UserController@getUserById']);
        $router->put('/{id}', ['uses' => 'UserController@update']);
        $router->delete('/{id}', ['uses' => 'UserController@delete']);
    });
});

$router->group(['middleware' => 'auth:admin'], function () use ($router) {
    $router->group(['prefix' => 'users'], function () use ($router) {
        $router->get('/', ['uses' => 'UserController@index']);
        $router->post('/restore', ['uses' => 'UserController@restore']);
    });
});

/*
|--------------------------------------------------------------------------
| Category
|--------------------------------------------------------------------------
|*/

$router->group(['prefix' => 'categories'], function () use ($router) {
    $router->get('/', ['uses' => 'CategoryController@get']);
});

// Admin only
$router->group(['middleware' => 'auth:admin'], function () use ($router) {
    $router->group(['prefix' => 'categories'], function () use ($router) {
        $router->post('/', ['uses' => 'CategoryController@insert']);
        $router->put('/{id}', ['uses' => 'CategoryController@update']);
        $router->delete('/{id}', ['uses' => 'CategoryController@delete']);
    });
});

/*
|--------------------------------------------------------------------------
| Publisher
|--------------------------------------------------------------------------
|*/

$router->group(['prefix' => 'publishers'], function () use ($router) {
    $router->get('/', ['uses' => 'PublisherController@get']);
    $router->get('/{id}', ['uses' => 'PublisherController@getPublisherById']);
});

// Admin Only
$router->group(['middleware' => 'auth:admin'], function () use ($router) {
    $router->group(['prefix' => 'publishers'], function () use ($router) {
        $router->post('/', ['uses' => 'PublisherController@insert']);
        $router->put('/{id}', ['uses' => 'PublisherController@update']);
        $router->delete('/{id}', ['uses' => 'PublisherController@delete']);
    });
});


/*
|--------------------------------------------------------------------------
| Language
|--------------------------------------------------------------------------
|*/

$router->group(['prefix' => 'languages'], function () use ($router) {
    $router->get('/', ['uses' => 'LanguageController@getLanguageAll']);
});

// Admin only
$router->group(['middleware' => 'auth:admin'], function () use ($router) {
    $router->group(['prefix' => 'languages'], function () use ($router) {
        $router->get('/{id}', ['uses' => 'LanguageController@getLanguageById']);
        $router->post('/', ['uses' => 'LanguageController@insert']);
        $router->put('/{id}', ['uses' => 'LanguageController@update']);
        $router->delete('/{id}', ['uses' => 'LanguageController@delete']);
    });
});

/*
|--------------------------------------------------------------------------
| Book
|--------------------------------------------------------------------------
|*/

$router->group(['prefix' => 'books'], function () use ($router) {
    $router->get('/', ['uses' => 'BookController@index']);
    $router->get('/search', ['uses' => 'BookController@getByKeyword']);
    $router->get('/{id}', ['uses' => 'BookController@get']);
});


// Admin only
$router->group(['middleware' => 'auth:admin'], function () use ($router) {
    $router->group(['prefix' => 'books'], function () use ($router) {
        $router->post('/', ['uses' => 'BookController@insert']);
        $router->put('/{id}', ['uses' => 'BookController@update']);
        $router->delete('/{id}', ['uses' => 'BookController@delete']);
        $router->post('/restore', ['uses' => 'BookController@restore']);
    });
});

/*
|--------------------------------------------------------------------------
| Transaction
|--------------------------------------------------------------------------
|*/

$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->group(['prefix' => 'transactions'], function () use ($router) {
        $router->get('/', ['uses' => 'TransactionController@index']);
        $router->get('/{id}', ['uses' => 'TransactionController@get']);
        $router->post('/', ['uses' => 'TransactionController@insert']);
    });
});

$router->group(['middleware' => 'auth:admin'], function () use ($router) {
    $router->group(['prefix' => 'transactions'], function () use ($router) {
        $router->put('/{id}', ['uses' => 'TransactionController@return']);
    });
});

// $router->group(['middleware' => 'auth:user'], function () use ($router) {
//     $router->group(['prefix' => 'transactions'], function () use ($router) {
//         $router->post('/', ['uses' => 'TransactionController@insert']);
//     });
// });
