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

    public function getDetailJudul($request = []) {
        $param = new stdClass;
        $id_mhs = htmlspecialchars(trim($request['id_mhs']));

        try {
            $query = "SELECT us.id as id_user, us.no_identitas, us.nama, k.nama_kelompok, pd.id as id_dospem,
                        dospem.nama AS nama_dospem, master_lomba.nama_lomba, pd.status AS status_dospem, sj.status AS status_judul, 
                        sj.judul, sj.review, sj.submit_date
                        FROM users AS us 
                        JOIN kelompok AS k ON us.id = k.id_mhs 
                        LEFT JOIN pemilihan_dospem AS pd ON pd.id_mhs = us.id
                        LEFT JOIN users AS dospem ON dospem.id = pd.id_dosen 
                        LEFT JOIN submit_judul AS sj ON sj.id_dospem = pd.id
                        JOIN master_detail_lomba AS detail_lomba ON detail_lomba.id = k.id_detail_lomba 
                        JOIN master_lomba ON master_lomba.id = detail_lomba.id_mst_lomba 
                        WHERE us.id = :id_mhs
                        AND pd.created_at = (SELECT MAX(created_at) FROM pemilihan_dospem)
                        LIMIT 1";

            $result = $this->conn->prepare($query);
            $result->bindParam(":id_mhs", $id_mhs);
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
    

    public function pengajuanJudul($request = []) {
        $param = new stdClass;
        $id_dospem = htmlspecialchars(trim($request['id_dospem']));
        $txt_judul = htmlspecialchars($request['judul']);
        
        $this->conn->beginTransaction();
        try {
            $query = "INSERT INTO submit_judul(id_dospem, judul, submit_date, created_at) VALUES (:id_dospem, :judul, now(), now())";
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

    public function pengajuaRevisiJudul($request = []) {
        $param = new stdClass;
        $id_dospem = htmlspecialchars(trim($request['id_dospem']));
        $txt_judul = htmlspecialchars($request['judul']);
        
        $this->conn->beginTransaction();
        try {
            $query = "UPDATE submit_judul SET judul = :judul, status = 'Waiting Approval', submit_date = now(), updated_at = now() WHERE id_dospem = :id_dospem";
            $result = $this->conn->prepare($query);
            $result->bindParam(':id_dospem', $id_dospem);
            $result->bindParam(':judul', $txt_judul);
            $res = $result->execute();
            $this->conn->commit();

            if($res){
                $param->status_code = 200;
                $param->message = 'Success';
                $param->response = 'Berhasil Mengajukan Revisi Judul Ke Dosen Pembimbing.';
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
