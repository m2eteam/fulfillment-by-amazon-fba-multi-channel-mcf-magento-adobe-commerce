<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Account;

class RemoveService
{
    private Repository $accountRepository;
    private \M2E\AmazonMcf\Model\Product\RemoveService $productRemoveService;
    private \M2E\AmazonMcf\Model\Order\RemoveService $orderRemoveService;

    public function __construct(
        Repository $accountRepository,
        \M2E\AmazonMcf\Model\Product\RemoveService $productRemoveService,
        \M2E\AmazonMcf\Model\Order\RemoveService $orderRemoveService
    ) {
        $this->accountRepository = $accountRepository;
        $this->productRemoveService = $productRemoveService;
        $this->orderRemoveService = $orderRemoveService;
    }

    public function remove(\M2E\AmazonMcf\Model\Account $account): void
    {
        $this->productRemoveService->removeByAccount($account);
        $this->orderRemoveService->removeByAccount($account);
        $this->accountRepository->delete($account);
    }
}
