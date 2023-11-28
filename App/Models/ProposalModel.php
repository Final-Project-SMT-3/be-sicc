<?php

class ProposalModel {
    private $param;
    private $conn;
    private $uploadPath;

    public function __construct() {
        try{
            $this->conn = new PDO('mysql:host=localhost;dbname=db_cc', 'root', '');
        } catch(PDOException $e){
            die($e->getMessage());
        }
    }

    public function pengajuanProposal($request = []) {
        $param = new stdClass;
        $id_dospem = htmlspecialchars(trim($request['id_dospem']));
        $id_judul = htmlspecialchars(trim($request['id_judul']));
        $file_path = htmlspecialchars(trim($request['file_path'])); // Getting file path from the request
    
        // You can perform additional validations on $file_path if needed
    
        $this->conn->beginTransaction();
        try {
            $new_file_path = time() . '.pdf'; // Unique file name
    
            if (file_put_contents('../storage/proposal/'.$new_file_path, base64_decode($file_path))) {
                $query = "INSERT INTO submit_proposal(id_dospem, id_judul, path, created_at) VALUES (:id_dospem, :id_judul, :path_file, now())";
                $result = $this->conn->prepare($query);
                $result->bindParam(':id_dospem', $id_dospem);
                $result->bindParam(':id_judul', $id_judul);
                $result->bindParam(':path_file', $new_file_path); // Corrected binding
    
                $res = $result->execute();
                $this->conn->commit();
    
                if ($res) {
                    $param->status_code = 200;
                    $param->message = 'Success';
                    $param->response = 'Berhasil Mengajukan Proposal Ke Dosen Pembimbing.';
                } else {
                    $this->conn->rollBack();
                    $param->status_code = 500;
                    $param->message = 'Terjadi kesalahan.';
                    $param->response = '';
                }
            } else {
                $this->conn->rollBack();
                $param->status_code = 500;
                $param->message = 'Terjadi kesalahan saat menyalin file.';
                $param->response = '';
            }
        } catch (Exception $e) {
            $this->conn->rollBack();
            $param->status_code = 500;
            $param->message = 'Terjadi kesalahan. ' . $e->getMessage();
            $param->response = '';
        } catch (PDOException $e) {
            $this->conn->rollBack();
            $param->status_code = 500;
            $param->message = 'Terjadi kesalahan. ' . $e->getMessage();
            $param->response = '';
        } finally {
            return $param;
        }
    }
}