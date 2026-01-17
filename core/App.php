<?php

class App {
    protected $controller = 'HomeController';
    protected $method = 'index';
    protected $params = [];

    public function __construct() {
        $url = $this->parseUrl();

        // Determine controller
        $controllerName = 'HomeController';
        if (!empty($url[0])) {
            $controllerCandidate = ucwords($url[0]) . 'Controller';
            if (file_exists(APP . '/controllers/' . $controllerCandidate . '.php')) {
                $controllerName = $controllerCandidate;
                unset($url[0]);
            }
        }
        
        require_once APP . '/controllers/' . $controllerName . '.php';
        $this->controller = new $controllerName;

        // Determine method (support kebab-case and snake_case -> camelCase)
        $methodName = 'index';
        if (isset($url[1])) {
            $rawMethod = $url[1];
            $camelMethod = $this->toCamelCase($rawMethod);
            if (method_exists($this->controller, $camelMethod)) {
                $methodName = $camelMethod;
                unset($url[1]);
            } elseif (method_exists($this->controller, $rawMethod)) {
                // Fallback: allow exact method name if it exists
                $methodName = $rawMethod;
                unset($url[1]);
            }
        }
        $this->method = $methodName;


        // Get params
        $this->params = $url ? array_values($url) : [];

        // Call the controller method with params (safe fallback to home/index)
        if (!method_exists($this->controller, $this->method)) {
            // Fallback to HomeController@index to avoid fatal errors
            require_once APP . '/controllers/HomeController.php';
            $this->controller = new HomeController();
            $this->method = 'index';
            $this->params = [];
        }
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    public function parseUrl() {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
    }

    // Convert strings like "forgot-password" or "reset_password" to "forgotPassword" / "resetPassword"
    private function toCamelCase($string) {
        $string = strtolower($string);
        $parts = preg_split('/[-_]+/', $string);
        if (!$parts) return $string;
        $camel = array_shift($parts);
        foreach ($parts as $p) {
            $camel .= ucfirst($p);
        }
        return $camel;
    }
}
