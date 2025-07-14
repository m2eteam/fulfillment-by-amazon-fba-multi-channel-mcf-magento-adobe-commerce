<?php

namespace M2E\AmazonMcf\Model\Lock\Transactional;

class Manager
{
    private string $nick;
    private \Magento\Framework\App\ResourceConnection $resourceConnection;
    private Repository $repository;
    private \M2E\AmazonMcf\Model\ResourceModel\Lock\Transactional $transactionalResource;
    private \M2E\AmazonMcf\Model\Lock\TransactionalFactory $transactionalFactory;
    private bool $isTableLocked = false;
    private bool $isTransactionStarted = false;

    public function __construct(
        string $nick,
        Repository $repository,
        \M2E\AmazonMcf\Model\Lock\TransactionalFactory $transactionalFactory,
        \M2E\AmazonMcf\Model\ResourceModel\Lock\Transactional $transactionalResource,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        $this->nick = $nick;
        $this->repository = $repository;
        $this->transactionalResource = $transactionalResource;
        $this->resourceConnection = $resourceConnection;
        $this->transactionalFactory = $transactionalFactory;
    }

    public function __destruct()
    {
        $this->unlock();
    }

    // ----------------------------------------

    public function lock()
    {
        if ($this->getExclusiveLock()) {
            return;
        }

        $this->createExclusiveLock();
        $this->getExclusiveLock();
    }

    public function unlock()
    {
        $this->isTableLocked && $this->unlockTable();
        $this->isTransactionStarted && $this->commitTransaction();
    }

    // ----------------------------------------

    private function getExclusiveLock()
    {
        $this->startTransaction();

        $connection = $this->resourceConnection->getConnection();
        $lockId = (int)$connection->select()
                                  ->from($this->getTableName(), ['id'])
                                  ->where('nick = ?', $this->nick)
                                  ->forUpdate()
                                  ->query()->fetchColumn();

        if ($lockId) {
            return true;
        }

        $this->commitTransaction();

        return false;
    }

    private function createExclusiveLock(): void
    {
        $this->lockTable();
        if ($this->repository->findByNick($this->nick) === null) {
            $lock = $this->transactionalFactory->create();
            $lock->init($this->nick);
            $this->repository->save($lock);
        }

        $this->unlockTable();
    }

    // ----------------------------------------

    private function startTransaction()
    {
        $connection = $this->resourceConnection->getConnection();
        $connection->beginTransaction();

        $this->isTransactionStarted = true;
    }

    private function commitTransaction()
    {
        $connection = $this->resourceConnection->getConnection();
        $connection->commit();

        $this->isTransactionStarted = false;
    }

    // ----------------------------------------

    private function lockTable()
    {
        $connection = $this->resourceConnection->getConnection();
        $connection->query("LOCK TABLES `{$this->getTableName()}` WRITE");

        $this->isTableLocked = true;
    }

    private function unlockTable()
    {
        $connection = $this->resourceConnection->getConnection();
        $connection->query('UNLOCK TABLES');

        $this->isTableLocked = false;
    }

    private function getTableName(): string
    {
        return $this->transactionalResource->getMainTable();
    }

    // ----------------------------------------

    public function getNick()
    {
        return $this->nick;
    }

    // ----------------------------------------
}
