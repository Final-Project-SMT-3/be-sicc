<?php

class Model {
    private $conn;
    private $statement;
    public function __construct()
    {
        try{
            $this->conn = new PDO(DB_HOST, DB_USER, DB_PASS);
        } catch(PDOException $e){
            die($e->getMessage());
        }
    }

    public function getData($query){
        $this->statement = $this->conn->prepare($query);
        $this->statement->execute();
        return $this->statement->fetchAll(PDO::FETCH_ASSOC);
    }
}