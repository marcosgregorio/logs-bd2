<?php
$servidor = "localhost";
$porta = 5432;
$bancoDeDados = "logs";
$usuario = "postgres";
$senha = "postgres";

$conexao = pg_connect("host=$servidor port=$porta dbname=$bancoDeDados " . "user=$usuario password=$senha");
if (!$conexao) {
    die("Não foi possível se conectar ao banco de dados.");
}

$query = "SELECT * FROM teste";

$result = pg_query($conexao, $query);

if (!$result) {
    die("Erro na consulta: " . pg_last_error($conexao));
}

// Processar os resultados
while ($row = pg_fetch_assoc($result)) {
    echo "Id: " . $row['id'] . "\n";
}