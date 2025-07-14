<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Dashboard\Product;

class Calculator implements CalculatorInterface
{
    private \M2E\AmazonMcf\Model\Product\Repository $productRepository;

    public function __construct(\M2E\AmazonMcf\Model\Product\Repository $orderRepository)
    {
        $this->productRepository = $orderRepository;
    }

    public function getTotalCount(\M2E\AmazonMcf\Model\Account $account): int
    {
        return $this->productRepository->getCountByAccountId($account->getId());
    }

    public function getCountOfAvailable(\M2E\AmazonMcf\Model\Account $account): int
    {
        return $this->productRepository->getCountOfAvailableByAccountId($account->getId());
    }

    public function getCountOfEnabled(\M2E\AmazonMcf\Model\Account $account): int
    {
        return $this->productRepository->getContOfEnabledByAccountId($account->getId());
    }
}
