<?php

use Slim\Http\Request;
use Slim\Http\Response;
use App\Action;

// Routes

$app->get('/', function (Request $request, Response $response, array $args) {
    return $this->renderer->render($response, 'index.phtml', $args);
});

$apiPath = '/'.$settings['settings']['api']['path'].'/';

$app->delete($apiPath.'users', 'App\Action\UsersAction:delete');
$app->get($apiPath.'users', 'App\Action\UsersAction:get');
$app->post($apiPath.'users', 'App\Action\UsersAction:post');
$app->put($apiPath.'users', 'App\Action\UsersAction:put');
