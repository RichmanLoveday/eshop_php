<?php

class App {
    protected $controller = "home";
    protected $method = "index";
    protected $params;

    public function __construct() {
        $url = $this->parseURL();
        // show($url);

        // URL Router design 
        // 1) Check for controller exist
        if(file_exists("../app/controllers/". strtolower($url[0] . ".php"))) {
          //  die;
            $this->controller = strtolower($url[0]);
            unset($url[0]);
        }

        require "../app/controllers/". strtolower($this->controller . ".php");
        $this->controller = new $this->controller;

        // 2) check if method exist
        $url[1] = isset($url[1]) ? strtolower($url[1]) : '';
        if(isset($url[1])) {
            if(method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        // Store parameters
        $this->params = (count($url) > 0) ? $url : ["home"];
        // show($this->params);
        // call method function
        call_user_func_array([$this->controller, $this->method], $this->params);
        
    }

    private function parseURL() {
        $url = isset($_GET['url']) ? $_GET['url'] : "home";
        return explode('/', filter_var(trim($url, '/'), FILTER_SANITIZE_URL));
    }
    
}


?>