<?php


class UsersController extends Controller{
    private $model;

    public function __construct()
    {
        require_once '../App/Models/UsersModel.php';
        $this->model = new UsersModel;
    }

    public function login(){
        if(isset($_SERVER['HTTP_HTTP_TOKEN'])){
            if($_SERVER['HTTP_HTTP_TOKEN'] == $this->getToken()){
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

    public function getDataUser(){
        if(isset($_SERVER['HTTP_HTTP_TOKEN'])){
            if($_SERVER['HTTP_HTTP_TOKEN'] == $this->getToken()){
                echo $this->model->getDataUser($_POST);
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

    public function register(){
        if(isset($_SERVER['HTTP_HTTP_TOKEN'])){
            if($_SERVER['HTTP_HTTP_TOKEN'] == $this->getToken()){
                echo $this->model->register($_POST);
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