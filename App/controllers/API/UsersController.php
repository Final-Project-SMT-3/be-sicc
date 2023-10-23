<?php


class UsersController extends Controller{
    private $model;
    private $token;

    public function __construct()
    {
        require_once '../App/Models/UsersModel.php';
        $this->model = new UsersModel;
        $this->token = 'KgncmLUc7qvicKI1OjaLYLkPi';
    }

    public function login(){
        if(isset($_SERVER['HTTP_HTTP_TOKEN'])){
            if($_SERVER['HTTP_HTTP_TOKEN'] == $this->token){
                echo $this->model->login($_POST);
            } else{
                $response = new stdClass;
                $response->code = 403;
                $response->message = 'Access Forbidden.';

                echo json_encode($response);
            }
        } else{
            $response = new stdClass;
            $response->code = 403;
            $response->message = 'Access Forbidden.';

            echo json_encode($response);
        }
    }
}