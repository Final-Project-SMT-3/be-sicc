<?php   

class Controller{
    public function view($view, $data = array()){
        require_once '../be-sicc/views/' . $view . '.php';
    }
}