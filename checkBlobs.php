<?php
declare(strict_types = 1);

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Proton\Finder\BlobInconsistencyFinder;

require_once __DIR__ . '/vendor/autoload.php';

error_reporting(E_ALL);

try {
    $logger = new Logger('Proton');
    $logger->pushHandler(new StreamHandler('log/log.log', Logger::DEBUG));
    $logger->pushHandler(new StreamHandler('log/alert.log', Logger::ALERT));

    $offset = isset($argv[1]) ? (int)$argv[1] : 0;
    $limit = isset($argv[2]) ? (int)$argv[2] : null;

    $blobsChecker = new BlobInconsistencyFinder('config/databases.ini', 'config/tables.ini', $logger);
    $blobsChecker->check($offset, $limit);
} catch (Exception $e) {
    print_r($e);
}


