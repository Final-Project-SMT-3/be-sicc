<?php

class DospemModel{
    private $param;
    private $conn;
    private $statement;

    public function __construct()
    {
        try{
            $this->conn = new PDO('mysql:host=localhost;dbname=db_cc', 'root', '');
            $this->param = new stdClass;
        } catch(PDOException $e){
            die($e->getMessage());
        }
    }

    public function getDospem(){
        try{
            $query = "SELECT * FROM users where tipe = 'dosen'";
            $result = $this->conn->prepare($query);
            $result->execute();
            $result->setFetchMode(PDO::FETCH_ASSOC);
            $res = $result->fetchAll();
            if($res) {
                $this->param->status_code = 200;
                $this->param->message = 'Success';
                $this->param->response = $res;
            } else {
                $this->param->status_code = 200;
                $this->param->message = 'Data tidak ditemukan';
                $this->param->response = '';
            }
        } catch(PDOException $e){
            $this->param->status_code = 500;
            $this->param->message = 'Terjadi kesalahan. ' . $e->getMessage();
            $this->param->response = '';
            
        } catch(Exception $e){
            $this->param->status_code = 500;
            $this->param->message = 'Terjadi kesalahan. ' . $e->getMessage();
            $this->param->response = '';
        } finally {
            return $this->param;
        }
    } 

    public function getDetailDospem($request = []) {
        $param = new stdClass();

        $id_dosen = htmlspecialchars(trim($request['id_dosen']));

        try {
            $query = "SELECT * FROM users where id = :id_dosen";

            $result = $this->conn->prepare($query);
            $result->bindParam(":id_dosen", $id_dosen);
            $result->execute();
            $result->setFetchMode(PDO::FETCH_ASSOC);
            $res = $result->fetchAll();

            if($res){
                $param->status_code = 200;
                $param->message = 'Success';
                $param->response = $res[0];
            } else{
                $param->status_code = 200;
                $param->message = 'Data tidak ditemukan';
                $param->response = '';
            }
        } catch(Exception $e){
            $param->status_code = 500;
            $param->message = 'Terjadi kesalahan. ' . $e->getMessage();
            $param->response = '';           
        } catch(PDOException $e){
            $param->status_code = 500;
            $param->message = 'Terjadi kesalahan. ' . $e->getMessage();
            $param->response = '';           
        } finally{
            return json_encode($param);
        }
    }

    public function pengajuanDospem($request = []){
        $this->conn->beginTransaction();
        try{
            $id_mhs = htmlspecialchars(trim($request['id_mhs']));
            $id_dosen = htmlspecialchars(trim($request['id_dosen']));
            $query = "INSERT INTO pemilihan_dospem(id_mhs, id_dosen, created_at) values (:id_mhs, :id_dosen, now())";
            $result = $this->conn->prepare($query);
            $result->bindParam(':id_mhs', $id_mhs);
            $result->bindParam(':id_dosen', $id_dosen);
            $res = $result->execute();
            $this->conn->commit();
            if($res){
                $this->param->status = 200;
                $this->param->message = 'Success';
                $this->param->response = 'Berhasil menambahkan data pengajuan dosen pembimbing baru.';
            } else {
                $this->conn->rollBack();
                $this->param->status = 500;
                $this->param->message = 'Terjadi kesalahan.';
                $this->param->response = '';
            }
        } catch(Exception $e){
            $this->conn->rollBack();
            $this->param->status_code = 500;
            $this->param->message = 'Terjadi kesalahan. ' . $e->getMessage();
            $this->param->response = '';
        } catch(PDOException $e){
            $this->conn->rollBack();
            $this->param->status_code = 500;
            $this->param->message = 'Terjadi kesalahan. ' . $e->getMessage();
            $this->param->response = '';
        } finally{
            return $this->param;
        }
    }
}