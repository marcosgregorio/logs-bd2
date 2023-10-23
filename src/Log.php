<?php

final class Log {
    private $file;

    public function __construct($logPath) {
        $this->file = fopen($logPath,"r");
    }

    public function getLogLines(): array {
        $lines = [];
        while (!feof($this->file)) {
            $line = fgets($this->file);
            $lines[] = $line;
        }

        return $lines;
    }

    public function getLogLinesBackwards(): array {
        $lines = array_reverse($this->getLogLines());
        
        return $lines;
    }
}