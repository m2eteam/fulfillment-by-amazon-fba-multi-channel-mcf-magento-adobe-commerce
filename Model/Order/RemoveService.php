<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Order;

class RemoveService
{
    private Log\RemoveService $orderLogRemoveService;
    private Repository $orderRepository;
    private Item\Repository $orderItemRepository;

    public function __construct(
        \M2E\AmazonMcf\Model\Order\Log\RemoveService $orderLogRemoveService,
        Repository $orderRepository,
        Item\Repository $orderItemRepository
    ) {
        $this->orderLogRemoveService = $orderLogRemoveService;
        $this->orderRepository = $orderRepository;
        $this->orderItemRepository = $orderItemRepository;
    }

    public function removeByAccount(\M2E\AmazonMcf\Model\Account $account): void
    {
        $this->orderLogRemoveService->removeByAccount($account);
        $this->orderItemRepository->deleteByAccountId($account->getId());
        $this->orderRepository->deleteByAccountId($account->getId());
    }
}
