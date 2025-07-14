<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Account;

class Repository
{
    private \M2E\AmazonMcf\Model\ResourceModel\Account $accountResource;
    private \M2E\AmazonMcf\Model\ResourceModel\Account\CollectionFactory $accountCollectionFactory;

    private bool $isLoaded = false;
    /** @var list<int, \M2E\AmazonMcf\Model\Account> */
    private array $loadedEntitiesById = [];
    /** @var list<string, \M2E\AmazonMcf\Model\Account> */
    private array $loadedEntitiesByMerchantId = [];

    public function __construct(
        \M2E\AmazonMcf\Model\ResourceModel\Account $accountResource,
        \M2E\AmazonMcf\Model\ResourceModel\Account\CollectionFactory $accountCollectionFactory
    ) {
        $this->accountResource = $accountResource;
        $this->accountCollectionFactory = $accountCollectionFactory;
    }

    public function create(\M2E\AmazonMcf\Model\Account $account): void
    {
        $this->accountResource->save($account);
        $this->isLoaded = false;
    }

    public function save(\M2E\AmazonMcf\Model\Account $account): void
    {
        $this->accountResource->save($account);
        $this->isLoaded = false;
    }

    public function delete(\M2E\AmazonMcf\Model\Account $account): void
    {
        $this->accountResource->delete($account);
        $this->isLoaded = false;
    }

    public function get(int $id): \M2E\AmazonMcf\Model\Account
    {
        $account = $this->find($id);
        if ($account === null) {
            throw new \LogicException(sprintf('Account with id %s not found.', $id));
        }

        return $account;
    }

    public function find(int $id): ?\M2E\AmazonMcf\Model\Account
    {
        $this->load();

        return $this->loadedEntitiesById[$id] ?? null;
    }

    public function findByMerchantId(string $merchantId): ?\M2E\AmazonMcf\Model\Account
    {
        $this->load();

        return $this->loadedEntitiesByMerchantId[$merchantId] ?? null;
    }

    /**
     * @return \M2E\AmazonMcf\Model\Account[]
     */
    public function getAll(): array
    {
        $this->load();

        return array_values($this->loadedEntitiesById);
    }

    private function load(): void
    {
        if ($this->isLoaded) {
            return;
        }

        $this->loadedEntitiesById = [];
        $this->loadedEntitiesByMerchantId = [];

        /** @var \M2E\AmazonMcf\Model\Account[] $accounts */
        $accounts = $this->accountCollectionFactory
            ->create()
            ->getItems();

        foreach ($accounts as $account) {
            $this->loadedEntitiesById[$account->getId()] = $account;
            $this->loadedEntitiesByMerchantId[$account->getMerchantId()] = $account;
        }

        $this->isLoaded = true;
    }
}
