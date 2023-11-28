<?php

class ProposalModel {
    private $param;
    private $conn;
    private $uploadPath;

    public function __construct() {
        try{
            $this->uploadPath = '../storage/proposal/';
            $this->conn = new PDO('mysql:host=localhost;dbname=db_cc', 'root', '');
        } catch(PDOException $e){
            die($e->getMessage());
        }
    }

    public function pengajuanProposal($request = []) {
        $param = new stdClass;
        $id_dospem = htmlspecialchars(trim($request['id_dospem']));
        $id_judul = htmlspecialchars(trim($request['id_judul']));

        // Getting file info from the request
        $fileinfo = pathinfo($_FILES['pdf']['name']);
        $extension = $fileinfo['extension'];
        $file_path = $this->generateFileName() . '.' . $extension; // Unique file name

        $this->conn->beginTransaction();
        try {
            // Upload file to the storage path
            move_uploaded_file($_FILES['pdf']['tmp_name'], $this->uploadPath . $file_path);

            $query = "INSERT INTO submit_proposal(id_dospem, id_judul, path, created_at) VALUES (:id_dospem, :id_judul, :path_file, now())";
            $result = $this->conn->prepare($query);
            $result->bindParam(':id_dospem', $id_dospem);
            $result->bindParam(':id_judul', $id_judul);
            $result->bindParam('path_file', $file_path);
            $res = $result->execute();
            $this->conn->commit();

            if($res){
                $param->status_code = 200;
                $param->message = 'Success';
                $param->response = 'Berhasil Mengajukan Proposal Ke Dosen Pembimbing.';
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

    private function generateFileName() {
        $query = "SELECT max(id) as id FROM submit_proposal";
    
        $result = $this->conn->prepare($query);
        $result->execute();
        $result->setFetchMode(PDO::FETCH_ASSOC);
        $res = $result->fetchAll();
    
        if (empty($res) || $res[0]['id'] === null) {
            return 1;
        } else {
            return intval($res[0]['id']) + 1;
        }
    }
}