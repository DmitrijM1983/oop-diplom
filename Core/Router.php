<?php

namespace Core;

use Controllers\EditController;
use Controllers\UserController;
use DI\ContainerBuilder;
use Exception;
use FastRoute;
use PDO;
use League;

class Router
{
    private FastRoute\Dispatcher $dispatcher;

    public function __construct()
    {
        $this->dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r)
        {
            $r->addRoute('GET', '/registration', [UserController::class, 'getRegForm']);
            $r->addRoute('POST', '/registration', [UserController::class, 'setRegData']);
            $r->addRoute('GET', '/login', [UserController::class, 'getLoginForm']);
            $r->addRoute('GET', '/logout', [UserController::class, 'logout']);
            $r->addRoute('POST', '/login', [UserController::class, 'getLogin']);
            $r->addRoute('GET', '/users', [UserController::class, 'getUsersList']);
            $r->addRoute('GET', '/create', [UserController::class, 'create']);
            $r->addRoute('POST', '/create', [UserController::class, 'createNewUser']);
            // {id} must be a number (\d+)
            $r->addRoute('GET', '/user/{id:\d+}', [UserController::class, 'getUserById']);

            $r->addRoute('GET', '/edit/{id:\d+}', [EditController::class, 'editUser']);
            $r->addRoute('POST', '/edit/{id:\d+}', [EditController::class, 'updateUser']);

            $r->addRoute('GET', '/security/{id:\d+}', [EditController::class, 'userSecurity']);
            $r->addRoute('POST', '/security/{id:\d+}', [EditController::class, 'updateUserSecurity']);

            $r->addRoute('GET', '/status/{id:\d+}', [EditController::class, 'getStatus']);
            $r->addRoute('POST', '/status/{id:\d+}', [EditController::class, 'setStatus']);

            $r->addRoute('GET', '/media/{id:\d+}', [EditController::class, 'getImage']);
            $r->addRoute('POST', '/media/{id:\d+}', [EditController::class, 'setImage']);

            $r->addRoute('GET', '/delete/{id:\d+}', [UserController::class, 'deleteUser']);
        });
    }

    /**
     * @return void
     * @throws Exception
     */
    public function getRoute(): void
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions(
            [
                PDO::class => function() {
                return new PDO('mysql:host=127.0.0.1;dbname=marlin;charset=utf8', 'root', '');
                },

                League\Plates\Engine::class => function() {
                return new League\Plates\Engine('views');
                }
            ]
        );

        $container = $containerBuilder->build();

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
                break;
            case FastRoute\Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
                $container->call($handler, [$vars]);
                break;
        }
    }
}

