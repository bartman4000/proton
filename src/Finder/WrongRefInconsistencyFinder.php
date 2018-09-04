<?php

namespace Proton\Finder;

use PDO;
use Proton\Inconsistency\WrongRefInconsistency;
use Proton\Table\Table;

class WrongRefInconsistencyFinder extends InconsistencyFinder
{
    public function check(string $tableToCheck = null): void
    {

        /** @var Table $table */
        foreach ($this->tables as $table) {
            if ($tableToCheck && $table->getName() !== $tableToCheck) {
                continue;
            }

            $this->checkReferencesInTable($table);
        }
    }

    private function checkReferencesInTable(Table $table): void
    {
        $this->logger->info("Checking references in table {$table->getName()}");

        $query = 'SELECT '.implode(',', $table->getRefColumns()).' FROM '.$table->getName();
        $pdo = $this->getDb($table->getDatabase());

        $stm = $pdo->query($query);
        while ($lazyObject = $stm->fetch(PDO::FETCH_LAZY)) {
            foreach ($table->getRefColumns() as $columnName) {
                $blobStorageId = $lazyObject->{$columnName};
                if (!$this->isBlob($blobStorageId)) {
                    $inconsistency = new WrongRefInconsistency($blobStorageId, $columnName, $table);
                    $this->logger->alert($inconsistency->getMessage());
                }
            }
        }
    }

    private function isBlob(int $blobStorageId): bool
    {
        $pdo = $this->getDb(InconsistencyFinder::PROTON_MAIL_GLOBAL);
        $stmt = $pdo->prepare("SELECT * FROM BlobStorage WHERE BlobStorageId = :blobStorageId");
        $stmt->execute(['blobStorageId' => $blobStorageId]);
        return $stmt->fetch() ? true : false;
    }
}
