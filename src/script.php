<?php

require_once 'DataBaseConnection.php';
function createTableFromMetaData() {
    $db = new DataBaseConnection();
    $dados = file_get_contents('dados/metadados.json');
    echo "$dados\n";
}