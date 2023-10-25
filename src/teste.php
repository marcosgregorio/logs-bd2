<?php

use Garden\Cli\Cli;
require_once 'vendor/autoload.php';
require_once 'Script.php'; 

$script = new Script();
$script->readLogFile();