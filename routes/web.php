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
    return env('JWT_KEY');
    // return $router->app->version();
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
| Category
|--------------------------------------------------------------------------
|*/

$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->group(['prefix' => 'categories'], function () use ($router) {
        $router->get('/', ['uses' => 'CategoryController@get']);
    });
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
| Book
|--------------------------------------------------------------------------
|*/
$router->group(['prefix' => 'books'], function () use ($router) {
    $router->get('/', ['uses' => 'BookController@getAllBooks']);

    $router->get('/{bookId}', ['uses' => 'BookController@getBookById']);
});

$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->group(['prefix' => 'users'], function () use ($router) {
        $router->get('/{userId}', ['uses' => 'UserController@getUserById']);

        $router->put('/{userId}', ['uses' => 'UserController@updateUser']);

        $router->delete('/{userId}', ['uses' => 'UserController@destroy']);
    });

    $router->group(['prefix' => 'transactions'], function () use ($router) {
        $router->get('/test', ['uses' => 'TransactionController@test']);
        
        $router->get('/', ['uses' => 'TransactionController@getAllTransaction']);

        $router->get('/{transactionId}', ['uses' => 'TransactionController@getTransactionId']);
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

        $router->post('/restore', ['uses' => 'BookController@restore']);
    });

    $router->group(['prefix' => 'transactions'], function () use ($router) {
        $router->put('/{transactionId}', ['uses' => 'TransactionController@update']);
    });
});

$router->group(['middleware' => 'auth:user'], function () use ($router) {
    $router->group(['prefix' => 'transactions'], function () use ($router) {
        $router->post('/', ['uses' => 'TransactionController@insert']);
    });
});

/*
|--------------------------------------------------------------------------
| Book
|--------------------------------------------------------------------------
|*/

$router->group(['prefix' => 'languages'], function () use ($router) {
    $router->get('/', ['uses' => 'LanguageController@getLanguageAll']);
});

// Admin only
$router->group(['middleware' => 'auth:admin'], function () use ($router) {
    $router->group(['prefix' => 'languages'], function () use ($router) {
        $router->get('/{id}', ['uses' => 'LanguageController@getLanguageById']);
        $router->post('/', ['uses' => 'LanguageController@insertLanguage']);
        $router->put('/{id}', ['uses' => 'LanguageController@updateLanguage']);
        $router->delete('/{id}', ['uses' => 'LanguageController@deleteLanguage']);
    });
});