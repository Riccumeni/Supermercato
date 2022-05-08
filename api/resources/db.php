<?php
    class Database{
        private $server = "localhost";
        private $db = "supermercato";
        private $username = "root";
        private $password = "";
        public $conn;
        
        public function getConnection(){
            try{
                $this->conn = new mysqli($this->server, $this->username, $this->password, $this->db);
            }catch(PDOException $e){
                echo "Errore di connessione";
            }
            return $this->conn;
        }
    }
?>