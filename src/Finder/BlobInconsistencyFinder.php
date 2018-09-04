<?php

namespace Proton\Finder;

use PDO;
use Proton\Inconsistency\Inconsistency;
use Proton\Inconsistency\NumRefBlobInconsistency;
use Proton\Inconsistency\OrphanBlobInconsistency;
use Proton\Table\Table;

class BlobInconsistencyFinder extends InconsistencyFinder
{

    /**
     * @param int $offset
     * @param int|null $limit
     */
    public function check(int $offset = 0, ?int $limit = null): void
    {
        $globalDb = $this->getDb(InconsistencyFinder::PROTON_MAIL_GLOBAL);
        $query = "SELECT BlobStorageID, NumReferences FROM BlobStorage";

        if ($limit) {
            $query .= " LIMIT {$offset}, {$limit}";
            $this->logger->info("Checking blobs from {$offset} to {$limit}");
        }

        $stmt = $globalDb->query($query);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $inconsistency = $this->validateBlob($row['BlobStorageID'], $row['NumReferences']);
            if (!is_null($inconsistency)) {
                $this->logger->alert($inconsistency->getMessage());
            }
        }
    }

    /**
     * @param int $blobStorageId
     * @param int $numReferences
     * @return null|Inconsistency
     */
    protected function validateBlob(int $blobStorageId, int $numReferences): ?Inconsistency
    {
        $actualRefCount = $this->countReferences($blobStorageId);

        if ($actualRefCount !== $numReferences) {
            $inconsistency = ($actualRefCount == 0) ? new OrphanBlobInconsistency($blobStorageId) : new NumRefBlobInconsistency($blobStorageId);
            return $inconsistency;
        }
        return null;
    }

    private function countReferences(int $blobStorageId): int
    {
        $count = 0;
        /** @var Table $table */
        foreach ($this->tables as $table) {
            $count += $this->countReferencesInTable($blobStorageId, $table);
        }
        return $count;
    }

    /**
     * @param int $blobStorageId
     * @param Table $table
     * @return int
     */
    private function countReferencesInTable(int $blobStorageId, Table $table): int
    {
        $query = "SELECT COUNT(1) FROM {$table->getName()} WHERE ";

        $column_binds = [];
        $params = [];
        foreach ($table->getRefColumns() as $column) {
            $column_binds[] = "`{$column}` = ?";
            $params[] = $blobStorageId;
        }

        if (!empty($column_binds)) {
            $query .= implode(' OR ', $column_binds);
            $pdo = $this->getDb($table->getDatabase());
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            return (int)$stmt->fetchColumn();
        } else {
            return 0;
        }
    }
}
