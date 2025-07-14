<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Account;

class SynchronizeService
{
    private \M2E\AmazonMcf\Model\Provider\Amazon\Account\Repository $accountProviderRepository;
    private Repository $accountRepository;
    private RemoveService $accountRemoveService;
    private \M2E\AmazonMcf\Model\AccountFactory $accountFactory;
    private \M2E\AmazonMcf\Model\Product\SynchronizeService $productSynchronizeService;

    public function __construct(
        Repository $accountRepository,
        \M2E\AmazonMcf\Model\Provider\Amazon\Account\Repository $accountProviderRepository,
        RemoveService $accountRemoveService,
        \M2E\AmazonMcf\Model\AccountFactory $accountFactory,
        \M2E\AmazonMcf\Model\Product\SynchronizeService $productSynchronizeService
    ) {
        $this->accountProviderRepository = $accountProviderRepository;
        $this->accountRepository = $accountRepository;
        $this->accountRemoveService = $accountRemoveService;
        $this->accountFactory = $accountFactory;
        $this->productSynchronizeService = $productSynchronizeService;
    }

    public function process(): void
    {
        $this->syncExistsAccounts();
        $this->syncNewAccounts();
    }

    private function syncExistsAccounts(): void
    {
        $allAccounts = $this->accountRepository->getAll();
        foreach ($allAccounts as $account) {
            $this->syncAccount($account);
        }
    }

    private function syncNewAccounts(): void
    {
        $providedItems = $this->accountProviderRepository->getAll();
        foreach ($providedItems as $providedItem) {
            if (
                !$providedItem->isEnabled()
                || $this->accountRepository->findByMerchantId($providedItem->getMerchantId()) !== null
            ) {
                continue;
            }

            $newAccount = $this->accountFactory
                ->create()
                ->init(
                    $providedItem->getMerchantId(),
                    $providedItem->getRegion()
                );

            $this->accountRepository->create($newAccount);
            $this->syncAccount($newAccount);
        }
    }

    public function syncAccount(\M2E\AmazonMcf\Model\Account $account): void
    {
        if (!$this->accountProviderRepository->isExists($account->getMerchantId())) {
            $this->accountRemoveService->remove($account);

            return;
        }

        $this->productSynchronizeService->sync($account);
    }
}
