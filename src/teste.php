<?php

// Require composer's autoloader.
require_once 'vendor/autoload.php';

$cli = new Cli();
// Define the cli options.

$cli->description('Implementa o mecanismo de log Redo com checkpoint usando o SGBD')
    ->opt('metadata:l', 'Caminho para um arquivo JSON com os dados para serem inseridos no SGBD.')
    ->opt('log:l', 'Caminho para um arquivo com os logs.');
