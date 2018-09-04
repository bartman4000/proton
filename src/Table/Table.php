<?php

namespace Proton\Table;

/**
 * Object representation of table with blob reference
 */
class Table
{
    /** @var string */
    protected $database;

    /** @var string */
    protected $name;

    /** @var array[string] */
    protected $refColumns;

    public function __construct(string $name, string $database, array $refColumns)
    {
        $this->name = $name;
        $this->database = $database;
        $this->refColumns = $refColumns;
    }

    /**
     * @return string
     */
    public function getDatabase(): string
    {
        return $this->database;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getRefColumns(): array
    {
        return $this->refColumns;
    }
}
