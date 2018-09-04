<?php

use Monolog\Logger;
use Proton\Database\NamedPDO;
use Proton\Finder\BlobInconsistencyFinder;
use Proton\Finder\InconsistencyFinder;
use Proton\Inconsistency\NumRefBlobInconsistency;
use Proton\Inconsistency\OrphanBlobInconsistency;
use Proton\Test\Invoke;

class BlobInconsistencyFinderTest extends PHPUnit_Framework_TestCase
{
    use Invoke;

    protected function getPdoMock($stmtMock, $dbName)
    {
        /** @var NamedPDO|PHPUnit_Framework_MockObject_MockObject $pdo */
        $pdo = $this->getMockBuilder(NamedPDO::class)->disableOriginalConstructor()->getMock();
        $pdo->method('getDbName')->willReturn($dbName);
        $pdo->method('prepare')->withAnyParameters()->willReturn($stmtMock);
        return $pdo;
    }

    /**
     * @throws ReflectionException
     */
    public function testValidateBlobNull()
    {
        /** @var Logger|PHPUnit_Framework_MockObject_MockObject $logger */
        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();

        $stmt = $this->getMockBuilder(PDOStatement::class)->disableOriginalConstructor()->getMock();
        $stmt->method('execute')->withAnyParameters()->willReturn(true);
        $stmt->method('fetchColumn')->willReturn(1);

        $globalPdo = $this->getPdoMock($stmt, InconsistencyFinder::PROTON_MAIL_GLOBAL);
        $shardPdo = $this->getPdoMock($stmt, InconsistencyFinder::PROTON_MAIL_SHARD);

        $finder = new BlobInconsistencyFinder('config/databases.ini', 'config/tables.ini', $logger);
        $this->setProperty($finder, 'pdos', [$globalPdo, $shardPdo]);

        $inconsistency = $this->invokeMethod($finder, 'validateBlob', array(34587, 6));
        $this->assertNull($inconsistency);
    }

    /**
     * @throws ReflectionException
     */
    public function testValidateBlobNumRef()
    {
        /** @var Logger|PHPUnit_Framework_MockObject_MockObject $logger */
        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();

        $stmt = $this->getMockBuilder(PDOStatement::class)->disableOriginalConstructor()->getMock();
        $stmt->method('execute')->withAnyParameters()->willReturn(true);
        $stmt->method('fetchColumn')->willReturn(1);

        $globalPdo = $this->getPdoMock($stmt, InconsistencyFinder::PROTON_MAIL_GLOBAL);
        $shardPdo = $this->getPdoMock($stmt, InconsistencyFinder::PROTON_MAIL_SHARD);

        $finder = new BlobInconsistencyFinder('config/databases.ini', 'config/tables.ini', $logger);
        $this->setProperty($finder, 'pdos', [$globalPdo, $shardPdo]);

        $inconsistency = $this->invokeMethod($finder, 'validateBlob', array(34587, 4));
        $this->assertInstanceOf(NumRefBlobInconsistency::class, $inconsistency);
    }

    /**
     * @throws ReflectionException
     */
    public function testValidateBlobOrphan()
    {
        /** @var Logger|PHPUnit_Framework_MockObject_MockObject $logger */
        $logger = $this->getMockBuilder(Logger::class)->disableOriginalConstructor()->getMock();

        $stmt = $this->getMockBuilder(PDOStatement::class)->disableOriginalConstructor()->getMock();
        $stmt->method('execute')->withAnyParameters()->willReturn(true);
        $stmt->method('fetchColumn')->willReturn(0);

        $globalPdo = $this->getPdoMock($stmt, InconsistencyFinder::PROTON_MAIL_GLOBAL);
        $shardPdo = $this->getPdoMock($stmt, InconsistencyFinder::PROTON_MAIL_SHARD);

        $finder = new BlobInconsistencyFinder('config/databases.ini', 'config/tables.ini', $logger);
        $this->setProperty($finder, 'pdos', [$globalPdo, $shardPdo]);

        $inconsistency = $this->invokeMethod($finder, 'validateBlob', array(34587, 2));
        $this->assertInstanceOf(OrphanBlobInconsistency::class, $inconsistency);
    }
}
