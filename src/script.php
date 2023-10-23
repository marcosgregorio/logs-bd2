<?php

require_once 'DataBaseConnection.php';
require_once 'Log.php';

function createTableFromMetaData(): void {
    new DataBaseConnection();
    echo "Criação da tabela finalizado..." . PHP_EOL;
}

function readLogFile(): void {
    $log = new Log("dados/entradaLog.txt");
    $lines = $log->getLogLinesBackwards();
    processLogs($lines);
}

function processLogs(array $lines): void {
    foreach ($lines as $line) {
        $line = trim($line);
        if (strpos($line, "")) {
            
        }
    }
}