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
        $password = htmlspecialchars(strip_tags(trim($request['password'])));
        $username = htmlspecialchars(strip_tags(trim($request['username'])));
        try{
            $query = "SELECT us.*, k.nama_kelompok, k.nim_anggota, k.nama_anggota, dospem.no_identitas, dospem.nama as nama_dospem, detail_lomba.detail_lomba FROM users as us join kelompok as k on us.id = k.id_mhs JOIN pemilihan_dospem as pd on pd.id_mhs = us.id join users as dospem on dospem.id = pd.id_dosen JOIN master_detail_lomba as detail_lomba ON detail_lomba.id = k.id_detail_lomba WHERE pd.status = 'Accept' AND password = :pass AND username = :user LIMIT 1";

            $result = $this->conn->prepare($query);
            $result->bindParam(":pass", $password);
            $result->bindParam(":user", $username);
            $result->execute();
            $result->setFetchMode(PDO::FETCH_ASSOC);
            $res = $result->fetchAll();
            // var_dump($res);
            if($res){
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