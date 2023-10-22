<?php

use Garden\Cli\Cli;
require_once 'vendor/autoload.php';
require_once 'script.php'; 

createTableFromMetaData();
// $cli = new Cli();
// $cli->description('Implementa o mecanismo de log Redo com checkpoint usando o SGBD')
// ->opt('metadata:l', 'Caminho para um arquivo JSON com os dados para serem inseridos no SGBD.')
// ->opt('log:l', 'Caminho para um arquivo com os logs.');