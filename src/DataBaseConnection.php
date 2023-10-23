<?php

use PgSql\Connection;
final class DataBaseConnection {
    private $connection;
    private string $server = "localhost";
    private int $port = 5432;
    private string $dataBase= "logs";
    private string $user = "postgres";
    private string $password = "postgres";

    public function __construct() {
        $connect = "host=$this->server port=$this->port dbname=$this->dataBase user=$this->user password=$this->password";
        $this->connection = pg_connect($connect)
            or die("Não foi possível se conectar ao banco de dados.");
        $this->createDefaultTable();        
    }

    private function createDefaultTable(): void {
        $this->dropTableTeste();

        $this->createTableTeste();

        $this->insertMetaDataIntoTeste();
    }

    private function dropTableTeste() {
        try {
            $dropTableSQL = "DROP TABLE IF EXISTS teste";
            pg_query($this->connection, $dropTableSQL);
        } catch (Throwable $th) {
            echo "Ocorreu um erro ao dropar a tabela" . PHP_EOL;
            throw $th;
        }
    }

    private function createTableTeste(): void {
        try {
            $createTableSQL = "CREATE TABLE teste (
                id serial PRIMARY KEY,
                A int,
                B int
            )";
            pg_query($this->connection, $createTableSQL);
        } catch (Throwable $th) {
            echo "Ocorreu um erro ao criar a tabela!" . PHP_EOL;
            throw $th;
        }
    }

    private function insertMetaDataIntoTeste(): void {
        $metaDados = json_decode(file_get_contents("dados/metadado.json"));
        $dados = (array) $metaDados->table;
        $length = count($dados["id"]);
        $rows = [];
        for ($i = 0; $i < $length; $i++) { 
            $row["id"] = $dados["id"][$i];
            $row["A"] = $dados["A"][$i];
            $row["B"] = $dados["B"][$i];
            $rows[] = $row; 
        } 

        foreach ($rows as $row) {
            $this->insertRow($row);
        }
    }

    private function insertRow(array $row): void {
        try {
            $insert = "INSERT INTO teste (id, A, B) 
                VALUES(". $row['id'] . "," . $row['A'] . "," . $row['B'] . ")";
            pg_query($this->connection, $insert);
        } catch (Throwable $th) {
            echo "Ocorreu um erro ao inserir dados na tabela!" . PHP_EOL;
            throw $th;
        }
    }
}