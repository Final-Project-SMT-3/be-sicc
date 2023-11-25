<?php

class pengajuanJudulController extends Controller {
    private $model;

    public function __construct() 
    {
        require_once '../App/Models/JudulModel.php';
        $this->model = new JudulModel;
    }

    public function pengajuanJudul() {
        if (isset($_SERVER['HTTP_HTTP_TOKEN'])) {
            if ($_SERVER['HTTP_HTTP_TOKEN'] == $this->getToken()) {
                echo json_encode($this->model->pengajuanJudul($_POST));
            } else {
                $response = new stdClass;
                $response->code = 403;
                $response->message = 'Access Forbidden.';

                echo json_encode($response);
            }
        } else {
            $response = new stdClass;
            $response->code = 403;
            $response->message = 'Access Forbidden.';

            echo json_encode($response);
        }
    }
}
