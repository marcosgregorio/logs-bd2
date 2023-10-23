<?php

require_once 'DataBaseConnection.php';
require_once 'Log.php';

final class Script {
    private DataBaseConnection $db;
    private Log $log;
    private array $trasactionsWithoutCommit;
    private array $operationsWithoutCommit;

    public function __construct(Type $var = null) {
        $this->createTableFromMetaData();
    }

    public function __destruct() {
        unset($this->db, $this->log);
    }

    private function createTableFromMetaData(): void {
        $this->db = new DataBaseConnection();
        echo "Criação da tabela finalizada..." . PHP_EOL;
    }

    public function readLogFile(): void {
        $this->log = new Log("dados/entradaLog.txt");
        $linesBackwards = $this->log->getLogLinesBackwards();
        $this->processLogs($linesBackwards);
    }

    private function processLogs(array $lines): void {
        $this->trasactionsWithoutCommit = $this->getTransactionsWithoutCommit($lines);
        $this->operationsWithoutCommit = $this->getOperationsFromTransactions($lines);
        $this->handleUndoOperations();
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

    private function getOperationsFromTransactions(array $lines): array {
        $operations = [];
        foreach ($this->trasactionsWithoutCommit as $key => $t) {
            $operations[] = array_filter($lines, function ($linha) use($t) {
                return strpos($linha, "<$t") !== false;
            });
        }
        
        return $operations;
    }

    private function handleUndoOperations() {
        foreach ($this->operationsWithoutCommit as $operation) {
            $pattern = '/<([^>]*)>/'; 

            $firstTransaction = end($operation);
            $hasMatch = preg_match($pattern, $firstTransaction, $matches);
            if ($hasMatch) {
                $match = $matches[1];
                if (!empty($match)) {
                    [$transaction, $id, $column, $value] = explode(',', $matches[1]);
                    $column = trim($column);
                    echo "Transacao: $transaction, id: $id, coluna: $column, valor: $value\n ";
                    $undoSQL = "UPDATE teste SET $column = $value WHERE id = $id";
                // pg_query($db->connection, $undoSQL);
                }
            }
        }
    }
}