<?php   

class App{
    private $controller;
    private $method;
    private $params;

    public function __construct()
    {
        $url = $this->parseUrl();
        $controllerName = $url[0] == 'API' ? $url[1] : $url[0];
        $methodName = $url[0] == 'API' ? $url[2] : $url[1];
        
        if($url[0] == 'API'){
            // FOR API CONTROLLER
            if(file_exists("../App/controllers/API/" . ucfirst($url[1]) . "Controller.php")){
                $this->controller = ucfirst($url[1]) . 'Controller'; 
                unset($url[1]);
            }

            // GET Controller
            require_once '../App/controllers/API/' . $this->controller . '.php';
            $this->controller = new $this->controller;

            if(isset($url[2])){
                if(method_exists($this->controller, $methodName)){
                    $this->method = $methodName;
                    unset($url[2]);
                }
            }
        } else {
            // FOR NON API CONTROLLER
            if(file_exists("../App/controllers/" . ucfirst($url[0]) . "Controller.php")){
                $this->controller = ucfirst(ucfirst($url[0])) . 'Controller'; 
                unset($url[0]);
            }

            // GET Controller
            require_once "../App/controllers/" . $this->controller . '.php';
            $this->controller = new $this->controller;
    
            if(isset($url[1])){
                if(method_exists($this->controller, $methodName)){
                    $this->method = $methodName;
                    unset($url[1]);
                }
            }
        }

        // Parsing parameter
        if(!empty($url)){
            $this->params = array_values($url);
        }

        // Run controler & method
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    public function parseUrl(){
        if( isset($_GET['url']) ){
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
    }
}