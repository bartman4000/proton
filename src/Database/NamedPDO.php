<?php

namespace Proton\Database;

use PDO;

class NamedPDO extends PDO
{
    /** @var string $dbname */
    private $dbname;

    public function __construct($dbname, $dsn, $username, $passwd, $options = [])
    {
        parent::__construct($dsn, $username, $passwd, $options);

        $this->dbname = $dbname;
    }

    /**
     * @return string
     */
    public function getDbName(): string
    {
        return $this->dbname;
    }
}
