<?php

namespace Proton\Finder;

use Proton\Database\ConfigIniParser;
use Proton\Database\NamedPDO;
use Psr\Log\LoggerInterface;

abstract class InconsistencyFinder
{
    const PROTON_MAIL_GLOBAL = 'ProtonMailGlobal';
    const PROTON_MAIL_SHARD = 'ProtonMailShard';

    /** @var array[NamedPDO] */
    protected $pdos;

    /** @var array[Table] */
    protected $tables;

    /** @var LoggerInterface $logger */
    protected $logger;

    public function __construct(string $databasesIni, string $tablesIni, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->pdos = ConfigIniParser::getDBS($databasesIni);
        $this->tables = ConfigIniParser::getTables($tablesIni);
    }

    abstract public function check();

    /**
     * @param string $database
     * @return NamedPDO
     */
    protected function getDb(string $database): NamedPDO
    {
        $arr = array_filter($this->pdos, function (NamedPDO $pdo) use ($database) {
            return $pdo->getDbName() === $database;
        });

        return array_shift($arr);
    }
}
