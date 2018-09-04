<?php

namespace Proton\Inconsistency;

class OrphanBlobInconsistency extends Inconsistency
{
    public function getMessage(): string
    {
        return "Orphan blob with BlobStorageID = {$this->rowId}";
    }
}
