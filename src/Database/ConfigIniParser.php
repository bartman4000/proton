<?php

namespace Proton\Database;

use http\Exception\InvalidArgumentException;
use Proton\Table\Table;

class ConfigIniParser
{
    public static function getDBS(string $dbsIni): array
    {
        $dbs = [];
        $ini_array = parse_ini_file($dbsIni, true);
        $ini_array = array_filter($ini_array, function ($array) {
            return key_exists('dsn', $array) && key_exists('user', $array)  && key_exists('pass', $array); //validate config
        });

        foreach ($ini_array as $dbname => $array) {
            $dbs[] = new NamedPDO($dbname, $array['dsn'], $array['user'], $array['pass']);
        }

        if (empty($dbs)) {
            throw new InvalidArgumentException("No defined databases in {$dbsIni} file");
        }

        return $dbs;
    }

    public static function getTables(string $tablesIni): array
    {
        $tables = [];
        $ini_array = parse_ini_file($tablesIni, true);
        $ini_array = array_filter($ini_array, function ($array) {
            return key_exists('database', $array) && key_exists('refcolumns', $array)  && is_array($array['refcolumns']); //validate config
        });

        foreach ($ini_array as $name => $array) {
            $tables[] = new Table($name, $array['database'], $array['refcolumns']);
        }

        if (empty($tables)) {
            throw new InvalidArgumentException("No defined tables in {$tablesIni} file");
        }

        return $tables;
    }
}
