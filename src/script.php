<?php

require_once 'DataBaseConnection.php';
require_once 'Log.php';

function createTableFromMetaData(): void {
    new DataBaseConnection();
    echo "Criação da tabela finalizado..." . PHP_EOL;
}

function readLogFile(): void {
    $log = new Log("dados/entradaLog.txt");
    $lines = $log->getLogLines();
    processLogs($lines);
    // $linesBackwards = $log->getLogLinesBackwards();
    // processLogs($linesBackwards);
}

function processLogs(array $lines): void {
    $newArray = $lines;
    $trasactionsWithoutCommit = getTransactionsWithoutCommit($lines);
    $operationsWithoutCommit =getOperationsFromTransactions($trasactionsWithoutCommit, ($lines));
}

function getTransactionsWithoutCommit(array $lines): array {
    $trasactionsWithoutCommit = [];
    $str = implode($lines);
    foreach ($lines as $key => $line) {
        $line = trim($line);
        if (strpos($line, "start")) {
            $transaction = substr($line, -3, 2);
            $regex = "/<commit $transaction>/";
            $dontHaveCommit = !preg_match($regex, $str);
            if ($dontHaveCommit) {
                echo "Transação $transaction realizou UNDO" . PHP_EOL;
                $trasactionsWithoutCommit[] = $transaction; 
            }
        }
    }
    return $trasactionsWithoutCommit;
}

function getOperationsFromTransactions(array $transactions, array $lines): array {
    $operations = [];
    foreach ($transactions as $key => $t) {
        $operations[] = array_filter($lines, function ($linha) use($t) {
            return strpos($linha, "<$t") !== false;
        });
    }
    
    return $operations;
}
