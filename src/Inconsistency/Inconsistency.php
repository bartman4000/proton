<?php

namespace Proton\Inconsistency;

abstract class Inconsistency
{
    /** @var int $rowId */
    protected $rowId;

    public function __construct(int $rowId)
    {
        $this->rowId = $rowId;
    }

    public function __toString()
    {
        return get_class($this).' in '.$this->rowId;
    }

    abstract public function getMessage(): string;
}
