<?php

class PengajuanDospemController extends Controller{
    private $response;
    private $model;

    public function __construct()
    {
        $this->response = new stdClass;
        require_once '../App/Models/DospemModel.php';
        $this->model = new DospemModel;
    }

    public function getDospem(){
        if(isset($_SERVER['HTTP_HTTP_TOKEN'])){
            if($_SERVER['HTTP_HTTP_TOKEN'] == $this->getToken()){
                echo json_encode($this->model->getDospem());
            } else{
                $this->response->code = 403;
                $this->response->message = 'Access Forbidden.';

                echo json_encode($this->response);
            }
        } else{
            $this->response->code = 403;
            $this->response->message = 'Access Forbidden.';

            echo json_encode($this->response);
        }
    }

    public function getDetailDospem(){
        if(isset($_SERVER['HTTP_HTTP_TOKEN'])){
            if($_SERVER['HTTP_HTTP_TOKEN'] == $this->getToken()){
                echo $this->model->getDetailDospem($_POST);
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

    public function pengajuanDospem(){
        if(isset($_SERVER['HTTP_HTTP_TOKEN'])){
            if($_SERVER['HTTP_HTTP_TOKEN'] == $this->getToken()){
                echo json_encode($this->model->pengajuanDospem($_POST));
            } else{
                $this->response->code = 403;
                $this->response->message = 'Access Forbidden.';

                echo json_encode($this->response);
            }
        } else{
            $this->response->code = 403;
            $this->response->message = 'Access Forbidden.';

            echo json_encode($this->response);
        }
    }
}