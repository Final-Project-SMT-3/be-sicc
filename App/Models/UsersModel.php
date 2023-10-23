<?php


class UsersModel{
    private $param;
    private $conn;
    private $statement;

    public function __construct()
    {
        try{
            $this->conn = new PDO('mysql:host=localhost;dbname=db_cc', 'root', '');
        } catch(PDOException $e){
            die($e->getMessage());
        }
    }

    public function getData($query){
        $this->statement = $this->conn->prepare($query);
        $this->statement->execute();
        return $this->statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function login($request = []){
        $param = new stdClass();
        // $password = '-.' . md5($request['password']) . '.-';
        $password = trim($request['password']);
        $username = trim($request['username']);
        try{
            $res = $this->getData("SELECT * FROM users WHERE password = '$password' AND username = '$username' LIMIT 1");
            // var_dump($res);
            if(count($res) > 0){
                $param->status_code = 200;
                $param->message = 'Success';
                $param->response = $res[0];
            } else{
                $param->status_code = 200;
                $param->message = 'Data tidak ditemukan.';
                $param->response = null;
            }
        } catch(PDOException $e){
            $param->status_code = 500;
            $param->message = 'Server Error. ' . $e->getMessage();
        } finally{
            return json_encode($param);
        }

    }
}