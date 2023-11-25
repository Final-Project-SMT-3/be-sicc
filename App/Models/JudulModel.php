<?php

class JudulModel {
    private $param;
    private $conn;

    public function __construct() {
        try{
            $this->conn = new PDO('mysql:host=localhost;dbname=db_cc', 'root', '');
        } catch(PDOException $e){
            die($e->getMessage());
        }
    }

    public function pengajuanJudul($request = []) {
        $param = new stdClass;

        $id_dospem = htmlspecialchars(trim($request['id_dospem']));
        $txt_judul = htmlspecialchars($request['judul']);
        
        $this->conn->beginTransaction();

        try {
            $query = "INSERT INTO submit_judul(id_dospem, judul, created_at) VALUES (:id_dospem, :judul, now())";
            $result = $this->conn->prepare($query);
            $result->bindParam(':id_dospem', $id_dospem);
            $result->bindParam(':judul', $txt_judul);
            $res = $result->execute();
            $this->conn->commit();

            if($res){
                $param->status_code = 200;
                $param->message = 'Success';
                $param->response = 'Berhasil Mengajukan Judul Ke Dosen Pembimbing.';
            } else {
                $this->conn->rollBack();
                $param->status_code = 500;
                $param->message = 'Terjadi kesalahan.';
                $param->response = '';
            }
        } catch(Exception $e){
            $this->conn->rollBack();
            $param->status_code = 500;
            $param->message = 'Terjadi kesalahan. ' . $e->getMessage();
            $param->response = '';
        } catch(PDOException $e){
            $this->conn->rollBack();
            $param->status_code = 500;
            $param->message = 'Terjadi kesalahan. ' . $e->getMessage();
            $param->response = '';
        } finally{
            return $param;
        }
    }
}

