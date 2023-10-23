<?php

require_once 'DataBaseConnection.php';
require_once 'Log.php';

class Script {
    private DataBaseConnection $db;

    public function __construct(Type $var = null) {
        $this->var = $var;
    }

    private function createTableFromMetaData(): void {
        $db = new DataBaseConnection();
        echo "Criação da tabela finalizada..." . PHP_EOL;
    }

    public function readLogFile(): void {
        $log = new Log("dados/entradaLog.txt");
        $linesBackwards = $log->getLogLinesBackwards();
        processLogs($linesBackwards);
        unset($log);
    }

    private function processLogs(array $lines): void {
        $trasactionsWithoutCommit = getTransactionsWithoutCommit($lines);
        $operationsWithoutCommit = getOperationsFromTransactions($trasactionsWithoutCommit, ($lines));
        handleUndoOperations($operationsWithoutCommit);
    }

    private function getTransactionsWithoutCommit(array $lines): array {
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

    private function getOperationsFromTransactions(array $transactions, array $lines): array {
        $operations = [];
        foreach ($transactions as $key => $t) {
            $operations[] = array_filter($lines, function ($linha) use($t) {
                return strpos($linha, "<$t") !== false;
            });
        }
        
        return $operations;
    }

    private function handleUndoOperations(array $operations) {
        foreach ($operations as $operation) {
            $pattern = '/<([^>]*)>/'; 

            $firstTransaction = end($operation);
            $hasMatch = preg_match($pattern, $firstTransaction, $matches);
            if ($hasMatch) {
                $match = $matches[1];
                if (!empty($match)) {
                    [$transaction, $id, $column, $value] = explode(',', $matches[1]);
                    $column = trim($column);
                    echo "Transacao: $transaction \n $id  \n $column \n $value\n ";
                    $undoSQL = "UPDATE teste SET $column = $value WHERE id = $id";
                // pg_query($db->connection, $undoSQL);
                }
            }
        }
    }
}