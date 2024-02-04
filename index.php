<?php

require_once 'vendor/league/plates/src/Engine.php';
use Controllers\UserController;

require_once 'vendor/autoload.php';
//d($_POST); exit;
$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r)
{
    $r->addRoute('GET', '/users', [UserController::class, 'getUsersList']);
    $r->addRoute('GET', '/us', [UserController::class, 'insertData']);
    // {id} must be a number (\d+)
    $r->addRoute('GET', '/user/{id:\d+}', [UserController::class, 'getUserById']);
    $r->addRoute('GET', '/edit/{id:\d+}', [UserController::class, 'editUser']);
    $r->addRoute('POST', '/edit/{id:\d+}', [UserController::class, 'updateUser']);
    $r->addRoute('GET', '/security/{id:\d+}', [UserController::class, 'userSecurity']);
    $r->addRoute('POST', '/security/{id:\d+}', [UserController::class, 'updateUserSecurity']);
    $r->addRoute('GET', '/status/{id:\d+}', [UserController::class, 'getStatus']);
    $r->addRoute('POST', '/status/{id:\d+}', [UserController::class, 'setStatus']);
    $r->addRoute('GET', '/media/{id:\d+}', [UserController::class, 'getMedia']);
    $r->addRoute('POST', '/media/{id:\d+}', [UserController::class, 'setMedia']);
    // The /{title} suffix is optional
    $r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?'))
{
    $uri = substr($uri, 0, $pos);
}

$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        echo 'Нe тот метод!';
        d( $_SERVER['REQUEST_METHOD']);
        // ... 405 Method Not Allowed
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        $controller = new $handler[0];
        $action = $handler[1];
        $controller->$action($vars ?? '');
        break;
}
