<?php
declare(strict_types = 1);

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Proton\Database\ConfigIniParser;
use Proton\Finder\WrongRefInconsistencyFinder;

require_once __DIR__ . '/vendor/autoload.php';

error_reporting(E_ALL);

$logger = new Logger('Proton');
try {
    $logger->pushHandler(new StreamHandler('log/log.log', Logger::DEBUG));
    $logger->pushHandler(new StreamHandler('log/alert.log', Logger::ALERT));

    $dbs = ConfigIniParser::getDBS('config/databases.ini');

    $tableTocheck = isset($argv[1]) ? $argv[1] : null;

    $refsChecker = new WrongRefInconsistencyFinder('config/databases.ini', 'config/tables.ini', $logger);
    $refsChecker->check($tableTocheck);

} catch (Exception $e) {
    print_r($e);
}



