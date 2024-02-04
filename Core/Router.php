<?php
//
//namespace Core;
//
//use Controllers\UserController;
//use FastRoute;
//
//class Router
//{
//    public $dispatcher;
//    public string $httpMethod, $uri;
//    private
//
//
//    public function __construct(string $httpMethod, string $uri)
//    {
//        $this->httpMethod = $httpMethod;
//        $this->uri = $uri;
//    }
//
//
//    public function addNewRoute()
//    {
//        FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r)
//        {
//            $r->addRoute('GET', '/users', [UserController::class, 'getUsersList']);
//            // {id} must be a number (\d+)
//            $r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler');
//            // The /{title} suffix is optional
//            $r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');
//        });
//    }
//
//    public function getRoute()
//    {
//        $routeInfo = $this->addNewRoute()->dispatch($this->httpMethod, $this->uri);
////d($routeInfo);
//        switch ($routeInfo[0]) {
//            case FastRoute\Dispatcher::NOT_FOUND:
//                // ... 404 Not Found
//                break;
//            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
//                $allowedMethods = $routeInfo[1];
//                // ... 405 Method Not Allowed
//                break;
//            case FastRoute\Dispatcher::FOUND:
//                $handler = $routeInfo[1];
//                $vars = $routeInfo[2];
//                $controller = new $handler[0];
//                $action = $handler[1];
//                $controller->$action();
//                break;
//        }
//    }
//}
//
//
//    $dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r)
//    {
//        $r->addRoute('GET', '/users', [UserController::class, 'getUsersList']);
//        // {id} must be a number (\d+)
//        $r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler');
//        // The /{title} suffix is optional
//        $r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');
//    });
//
//
//
//    $routeInfo = $dispatcher->dispatch($httpMethod, $uri);
////d($routeInfo);
//    switch ($routeInfo[0]) {
//        case FastRoute\Dispatcher::NOT_FOUND:
//            // ... 404 Not Found
//            break;
//        case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
//            $allowedMethods = $routeInfo[1];
//            // ... 405 Method Not Allowed
//            break;
//        case FastRoute\Dispatcher::FOUND:
//            $handler = $routeInfo[1];
//            $vars = $routeInfo[2];
//            $controller = new $handler[0];
//            $action = $handler[1];
//            $controller->$action();
//            break;
//
//}