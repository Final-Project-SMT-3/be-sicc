<?php

class LombaModel{
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

    public function getAllData($request = []){
        try{
            // $query = "SELECT master_lomba.nama_lomba, pelaksanaan_lomba.*, detail_pelaksanaan_lomba.tanggal_mulai, detail_pelaksanaan_lomba.tanggal_akhir, detail_pelaksanaan_lomba.status FROM pelaksanaan_lomba JOIN detail_pelaksanaan_lomba ON detail_pelaksanaan_lomba.id_pelaksanaan_lomba = pelaksanaan_lomba.id JOIN master_lomba ON master_lomba.id = pelaksanaan_lomba.id_mst_lomba";
            $query = "SELECT * FROM master_lomba";

            $result = $this->conn->prepare($query);
            $result->execute();
            $result->setFetchMode(PDO::FETCH_ASSOC);
            $res = $result->fetchAll();
            if($res){
                foreach($res as $i => $item){
                    $queryDetail = "SELECT * FROM master_detail_lomba WHERE id_mst_lomba = " . $item['id'];
                    $resultDetail = $this->conn->prepare($queryDetail);
                    $resultDetail->execute();
                    $resultDetail->setFetchMode(PDO::FETCH_ASSOC);
                    $resDetail = $resultDetail->fetchAll();
                    if($resDetail){
                        $res[$i]['detailLomba'] = $resDetail;
                    }
                    
                    $queryPelaksanaan = "SELECT * FROM pelaksanaan_lomba WHERE id_mst_lomba = " . $item['id'];
                    $resultPelaksanaan = $this->conn->prepare($queryPelaksanaan);
                    $resultPelaksanaan->execute();
                    $resultPelaksanaan->setFetchMode(PDO::FETCH_ASSOC);
                    $resPelaksanaan = $resultPelaksanaan->fetchAll();
                    if($resPelaksanaan){
                        $res[$i]['detailPelaksanaan'] = $resPelaksanaan;
                    }
                }

                $this->param->status_code = 200;
                $this->param->message = 'Success';
                $this->param->response = $res;
            } else{
                $this->param->status_code = 200;
                $this->param->message = 'Data tidak ditemukan';
                $this->param->response = '';
            }
        }  catch(Exception $e){
            $this->param->status_code = 500;
            $this->param->message = 'Terjadi kesalahan. ' . $e->getMessage();
            $this->param->response = '';
        } catch(PDOException $e){
            $this->param->status_code = 500;
            $this->param->message = 'Terjadi kesalahan. ' . $e->getMessage();
            $this->param->response = '';
        }
        finally{
            return $this->param;
        }
    }

    public function getRequestedData($request = []){
        $param = new stdClass();

        $id_detail_lomba = htmlspecialchars(trim($request['id_lomba']));

        try{
            $query = "SELECT master_lomba.nama_lomba, master_detail_lomba.foto, master_detail_lomba.detail_lomba, pelaksanaan_lomba.tanggal, pelaksanaan_lomba.info, detail_pelaksanaan_lomba.status, detail_pelaksanaan_lomba.tanggal_mulai, detail_pelaksanaan_lomba.tanggal_akhir FROM pelaksanaan_lomba JOIN detail_pelaksanaan_lomba ON detail_pelaksanaan_lomba.id_pelaksanaan_lomba = pelaksanaan_lomba.id JOIN master_lomba ON master_lomba.id = pelaksanaan_lomba.id_mst_lomba JOIN master_detail_lomba on master_detail_lomba.id_mst_lomba = master_lomba.id where master_detail_lomba.id = :id";

            $result = $this->conn->prepare($query);
            $result->bindParam(":id", $id_detail_lomba);
            $result->execute();
            $result->setFetchMode(PDO::FETCH_ASSOC);
            $res = $result->fetchAll();

            if($res){
                $param->status_code = 200;
                $param->message = 'Success';
                $param->response = $res;
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
}