<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Product\Log;

class RemoveService
{
    private Repository $logRepository;

    public function __construct(Repository $logRepository)
    {
        $this->logRepository = $logRepository;
    }

    public function removeByProduct(\M2E\AmazonMcf\Model\Product $product): void
    {
        $this->removeByProductsIds([$product->getId()]);
    }

    /**
     * @param int[] $productsIds
     */
    public function removeByProductsIds(array $productsIds): void
    {
        $this->logRepository->deleteByProductIds($productsIds);
    }

    public function removeByAccount(\M2E\AmazonMcf\Model\Account $account): void
    {
        $this->logRepository->deleteByAccountId($account->getId());
    }
}
