<?php

namespace Proton\Inconsistency;

class NumRefBlobInconsistency extends Inconsistency
{
    public function getMessage(): string
    {
        return "Wrong value of NumReferences column for BlobStorageID = {$this->rowId}";
    }
}
