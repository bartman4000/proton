<?php

namespace Proton\Inconsistency;

use Proton\Table\Table;

class WrongRefInconsistency extends Inconsistency
{
    /**
     * @var Table
     */
    private $table;
    /**
     * @var string
     */
    private $columnName;

    public function __construct(int $rowId, string $columnName, Table $table)
    {
        parent::__construct($rowId);
        $this->rowId = $rowId;
        $this->table = $table;
        $this->columnName = $columnName;
    }

    public function getMessage(): string
    {
        return "Reference to missing blob Id {$this->rowId} in {$this->table->getDatabase()}.{$this->table->getName()}.{$this->columnName}";
    }
}
