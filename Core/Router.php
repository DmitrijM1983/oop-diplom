<?php

namespace Core;

use Controllers\AuthController;
use Controllers\EditController;
use Controllers\UserController;
use Controllers\SecurityController;
use Controllers\StatusController;
use Controllers\ImageController;
use FastRoute;

class Router
{
    private FastRoute\Dispatcher $dispatcher;

    public function __construct()
    {
        $this->dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r)
        {
            $r->addRoute('GET', '/registration', [AuthController::class, 'getRegForm']);
            $r->addRoute('POST', '/registration', [AuthController::class, 'setRegData']);
            $r->addRoute('GET', '/login', [AuthController::class, 'getLoginForm']);
            $r->addRoute('POST', '/login', [AuthController::class, 'getLogin']);

            $r->addRoute('GET', '/users', [UserController::class, 'getUsersList']);
            $r->addRoute('GET', '/create', [UserController::class, 'create']);
            $r->addRoute('POST', '/create', [UserController::class, 'createNewUser']);
            // {id} must be a number (\d+)
            $r->addRoute('GET', '/user/{id:\d+}', [UserController::class, 'getUserById']);

            $r->addRoute('GET', '/edit/{id:\d+}', [EditController::class, 'editUser']);
            $r->addRoute('POST', '/edit/{id:\d+}', [EditController::class, 'updateUser']);

            $r->addRoute('GET', '/security/{id:\d+}', [SecurityController::class, 'userSecurity']);
            $r->addRoute('POST', '/security/{id:\d+}', [SecurityController::class, 'updateUserSecurity']);

            $r->addRoute('GET', '/status/{id:\d+}', [StatusController::class, 'getStatus']);
            $r->addRoute('POST', '/status/{id:\d+}', [StatusController::class, 'setStatus']);

            $r->addRoute('GET', '/media/{id:\d+}', [ImageController::class, 'getImage']);
            $r->addRoute('POST', '/media/{id:\d+}', [ImageController::class, 'setImage']);

            $r->addRoute('GET', '/delete/{id:\d+}', [UserController::class, 'deleteUser']);
        });
    }

    /**
     * @return void
     */
    public function getRoute(): void
    {
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

        if (false !== $pos = strpos($uri, '?'))
        {
            $uri = substr($uri, 0, $pos);
        }

        $uri = rawurldecode($uri);

        $routeInfo = $this->dispatcher->dispatch($httpMethod, $uri);

        switch ($routeInfo[0]) {
            case FastRoute\Dispatcher::NOT_FOUND:
                break;
            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                d( $_SERVER['REQUEST_METHOD']);
                break;
            case FastRoute\Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
                $controller = new $handler[0];
                $action = $handler[1];
                $controller->$action($vars ?? '');
                break;
        }
    }
}

