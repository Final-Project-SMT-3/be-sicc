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

                $this->param->status = 200;
                $this->param->message = 'Berhasil menampilkan seluruh data';
                $this->param->data = $res;
            } else{
                $this->param->status = 200;
                $this->param->message = 'Data tidak ditemukan';
                $this->param->data = null;
            }
        }  catch(Exception $e){
            $this->param->status = 500;
            $this->param->message = 'Terjadi kesalahan. ' . $e->getMessage();
            $this->param->data = null;
        } catch(PDOException $e){
            $this->param->status = 500;
            $this->param->message = 'Terjadi kesalahan. ' . $e->getMessage();
            $this->param->data = null;
        }
        finally{
            return $this->param;
        }
    }

    public function getRequestedData($request = []){
        try{
            $query = "SELECT master_lomba.nama_lomba, pelaksanaan_lomba.*, detail_pelaksanaan_lomba.tanggal_mulai, detail_pelaksanaan_lomba.tanggal_akhir, detail_pelaksanaan_lomba.status FROM pelaksanaan_lomba JOIN detail_pelaksanaan_lomba ON detail_pelaksanaan_lomba.id_pelaksanaan_lomba = pelaksanaan_lomba.id JOIN master_lomba ON master_lomba.id = pelaksanaan_lomba.id_mst_lomba JOIN master_detail_lomba on master_detail_lomba.id_mst_lomba = master_lomba.id where master_detail_lomba.id = :id";

            $result = $this->conn->prepare($query);
            $result->bindParam(":id", $request['id_detail_lomba']);
            $result->execute();
            $result->setFetchMode(PDO::FETCH_ASSOC);
            $res = $result->fetchAll();
            if($res){
                $this->param->status = 200;
                $this->param->message = 'Berhasil menampilkan seluruh data';
                $this->param->data = $res;
            } else{
                $this->param->status = 200;
                $this->param->message = 'Data tidak ditemukan';
                $this->param->data = null;
            }
        } catch(Exception $e){
            $this->param->status = 500;
            $this->param->message = 'Terjadi kesalahan. ' . $e->getMessage();
            $this->param->data = null;           
        } catch(PDOException $e){
            $this->param->status = 500;
            $this->param->message = 'Terjadi kesalahan. ' . $e->getMessage();
            $this->param->data = null;           
        } finally{
            return $this->param;
        }
    }
}