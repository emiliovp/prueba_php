<?php
    class db{
        private $host = "localhost";
        private $user = "root";
        private $password = "";
        private $dbname = "escuela";

        public function conectar(){
            $con_mysql = "mysql:host=$this->host;dbname=$this->dbname";
            $conDB = new PDO($con_mysql, $this->user, $this->password);
            $conDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Para Codificación utf8_spanish2_ci
            $conDB->exec("SET NAMES UTF8");

            return $conDB;
        }
    }
?>