<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Account;

class CreateService
{
    private Repository $repository;
    private \M2E\AmazonMcf\Model\AccountFactory $accountFactory;
    /** @var \M2E\AmazonMcf\Model\Account\SynchronizeService */
    private SynchronizeService $synchronizeService;

    public function __construct(
        Repository $repository,
        \M2E\AmazonMcf\Model\AccountFactory $accountFactory,
        SynchronizeService $synchronizeService
    ) {
        $this->repository = $repository;
        $this->accountFactory = $accountFactory;
        $this->synchronizeService = $synchronizeService;
    }

    public function create(string $merchantId, string $region, bool $isEnabled): void
    {
        if (!\M2E\AmazonMcf\Model\Account::isFamousRegion($region)) {
            throw new \LogicException(sprintf('Region "%s" is not allowed.', $region));
        }

        $account = $this->updateOrCreate($merchantId, $region, $isEnabled);
        if ($account === null) {
            return;
        }

        $this->synchronizeService->syncAccount($account);
    }

    private function updateOrCreate(string $merchantId, string $region, bool $isEnabled): ?\M2E\AmazonMcf\Model\Account
    {
        $account = $this->repository->findByMerchantId($merchantId);
        if ($account !== null) {
            $this->update($isEnabled, $account);

            return $account;
        }

        if (!$isEnabled) {
            return null;
        }

        return $this->createNew($merchantId, $region);
    }

    private function update(bool $isEnabled, \M2E\AmazonMcf\Model\Account $account): void
    {
        if ($isEnabled) {
            $account->enable();
        } else {
            $account->disable();
        }

        $this->repository->save($account);
    }

    private function createNew(string $merchantId, string $region): \M2E\AmazonMcf\Model\Account
    {
        $account = $this->accountFactory->create();
        $account->init($merchantId, $region);
        $this->repository->create($account);

        return $account;
    }
}
