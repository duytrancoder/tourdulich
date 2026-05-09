<?php
namespace Api\Core;

class Router {
    private $routes = [];

    /**
     * Register a GET route
     */
    public function get($path, $handler) {
        $this->addRoute('GET', $path, $handler);
    }

    /**
     * Register a POST route
     */
    public function post($path, $handler) {
        $this->addRoute('POST', $path, $handler);
    }

    /**
     * Register a PUT route
     */
    public function put($path, $handler) {
        $this->addRoute('PUT', $path, $handler);
    }

    /**
     * Register a DELETE route
     */
    public function delete($path, $handler) {
        $this->addRoute('DELETE', $path, $handler);
    }

    private function addRoute($method, $path, $handler) {
        // Convert path to regex (e.g., /tours/{id} to /tours/([a-zA-Z0-9_-]+))
        $pathRegex = preg_replace('/\{([a-zA-Z0-9_-]+)\}/', '([a-zA-Z0-9_-]+)', $path);
        $pathRegex = '#^' . $pathRegex . '$#';
        
        $this->routes[] = [
            'method' => $method,
            'path' => $pathRegex,
            'handler' => $handler
        ];
    }

    /**
     * Dispatch the current request
     */
    public function dispatch($method, $uri) {
        // Clean URI from query parameters
        $uri = explode('?', $uri)[0];

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['path'], $uri, $matches)) {
                array_shift($matches); // Remove full match
                
                // Call the handler
                if (is_callable($route['handler'])) {
                    call_user_func_array($route['handler'], $matches);
                } else if (is_string($route['handler'])) {
                    // e.g. "AuthController@login"
                    list($controllerName, $methodName) = explode('@', $route['handler']);
                    $controllerClass = "\\Api\\Controllers\\" . $controllerName;
                    
                    if (class_exists($controllerClass)) {
                        $controller = new $controllerClass();
                        if (method_exists($controller, $methodName)) {
                            call_user_func_array([$controller, $methodName], $matches);
                        } else {
                            Response::error("Method not found", null, 500);
                        }
                    } else {
                        Response::error("Controller not found", null, 500);
                    }
                }
                return;
            }
        }

        // No route matched
        Response::error("Route not found", null, 404);
    }
}
