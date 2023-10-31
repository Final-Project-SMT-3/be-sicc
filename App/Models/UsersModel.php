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
            $query = "SELECT us.*, k.nama_kelompok, k.nim_anggota, k.nama_anggota, dospem.no_identitas as no_identitas_dosen, dospem.nama as nama_dospem, master_lomba.nama_lomba FROM users as us join kelompok as k on us.id = k.id_mhs JOIN pemilihan_dospem as pd on pd.id_mhs = us.id join users as dospem on dospem.id = pd.id_dosen JOIN master_detail_lomba as detail_lomba ON detail_lomba.id = k.id_detail_lomba join master_lomba on master_lomba.id = detail_lomba.id_mst_lomba WHERE pd.status = 'Accept' AND us.password = :pass AND us.username = :user LIMIT 1";

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
                $param->status = 'Sudah memilih dosen pembimbing.';
                $param->response = $res[0];
            } else{
                $query = "SELECT us.*, k.nama_kelompok, k.nim_anggota, k.nama_anggota, master_lomba.nama_lomba FROM users as us join kelompok as k on us.id = k.id_mhs JOIN master_detail_lomba as detail_lomba ON detail_lomba.id = k.id_detail_lomba join master_lomba on master_lomba.id = detail_lomba.id_mst_lomba WHERE us.password = :pass AND us.username = :user LIMIT 1";
                
                $result = $this->conn->prepare($query);
                $result->bindParam(":pass", $password);
                $result->bindParam(":user", $username);
                $result->execute();
                $result->setFetchMode(PDO::FETCH_ASSOC);
                $res = $result->fetchAll();
                if($res){
                    $param->status_code = 200;
                    $param->message = 'Success';
                    $param->message = 'Belum memilih dosen pembimbing.';
                    $param->response = $res[0];
                } else{
                    $param->status_code = 200;
                    $param->message = 'Data tidak ditemukan.';
                    $param->response = null;
                }
            }
        } catch(PDOException $e){
            $param->status_code = 500;
            $param->message = 'Server Error. ' . $e->getMessage();
        } finally{
            return json_encode($param);
        }

    }

    public function register($request = []){
        $param = new stdClass;
        // Param for users / ketua
        $nim = htmlspecialchars(trim($request['nim']));
        $nama = htmlspecialchars(trim($request['nama']));
        $username = htmlspecialchars(trim($request['username']));
        $password = md5(htmlspecialchars(trim($request['password'])));

        // Param for kelompok
        $id_detail_lomba = htmlspecialchars(trim($request['id_lomba']));
        $nama_kelompok = htmlspecialchars(trim($request['nama_kelompok']));
        $nama_anggota = trim($request['nama_anggota']);
        $nim_anggota = trim($request['nim_anggota']);
        $id_mhs = null;

        $this->conn->beginTransaction();
        try{
            $query = "INSERT INTO users(no_identitas, nama, username, password, tipe, created_at) value(:nim, :nama, :username, :password, 'mahasiswa', NOW())";
            $result = $this->conn->prepare($query);
            $result->bindParam(":nim", $nim);
            $result->bindParam(":nama", $nama);
            $result->bindParam(":username", $username);
            $result->bindParam(":password", $password);
            $res = $result->execute();
            if($res){
                $id_mhs = $this->conn->lastInsertId();
                $queryKelompok = "INSERT INTO kelompok(id_mhs, id_detail_lomba, nama_kelompok, nim_anggota, nama_anggota, created_at) VALUES(:id_mhs, :id_detail_lomba, :nama_kelompok, :nim_anggota, :nama_anggota, NOW())";
                $resultKelompok = $this->conn->prepare($queryKelompok);
                $resultKelompok->bindParam(":id_mhs", $id_mhs);
                $resultKelompok->bindParam(":id_detail_lomba", $id_detail_lomba);
                $resultKelompok->bindParam(":nama_kelompok", $nama_kelompok);
                $resultKelompok->bindParam(":nama_anggota", $nama_anggota);
                $resultKelompok->bindParam(":nim_anggota", $nim_anggota);
                $resKelompok = $resultKelompok->execute();
                $this->conn->commit();
                if($resKelompok){
                    $param->status = 200;
                    $param->message = 'Berhasil menambahkan kelompok baru.';
                    $param->response = '';
                } else{
                    $this->conn->rollBack();
                    $param->status = 500;
                    $param->message = 'Terjadi kesalahan.';
                    $param->response = '';
                }
            } else{
                $this->conn->rollBack();
                $param->status = 500;
                $param->message = 'Terjadi kesalahan.';
                $param->response = '';
            }
        } catch(Exception $e){
            $this->conn->rollBack();
            $param->status = 500;
            $param->message = 'Terjadi kesalahan. ' . $e->getMessage();
            $param->response = null;
        } catch(PDOException $e){
            $this->conn->rollBack();
            $param->status = 500;
            $param->message = 'Terjadi kesalahan. ' . $e->getMessage();
            $param->response = null;
        } finally {
            return json_encode($param);
        }
    }
}