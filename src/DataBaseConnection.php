<?php

use PgSql\Connection;
final class DataBaseConnection {
    private Connection $connection = null;
    private string $server = "localhost";
    private int $port = 5432;
    private string $dataBase= "logs";
    private string $user = "postgres";
    private string $password = "postgres";

    public function __construct() {
        $this->connection = pg_connect("host=$this->server port=$this->port dbname=$this->dataBase" . "user=$this->user password=$this->password")
            or die("Não foi possível se conectar ao banco de dados.");
        $this->testIfTableExists();        
    }

    private function testIfTableExists() {
        $createTableSQL = "CREATE TABLE IF NOT EXISTS teste (
            id serial PRIMARY KEY,
            A int,
            B int
        )";
        pg_query($this->connection, $createTableSQL);
    }
}