<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Product\Log;

class Repository
{
    private \M2E\AmazonMcf\Model\ResourceModel\Product\Log $logResource;
    private \M2E\AmazonMcf\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory;

    public function __construct(
        \M2E\AmazonMcf\Model\ResourceModel\Product\Log $logResource,
        \M2E\AmazonMcf\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
    ) {
        $this->logResource = $logResource;
        $this->productCollectionFactory = $productCollectionFactory;
    }

    public function create(\M2E\AmazonMcf\Model\Product\Log $log): void
    {
        $this->logResource->save($log);
    }

    public function deleteByAccountId(int $accountId): void
    {
        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addFieldToFilter(
            \M2E\AmazonMcf\Model\ResourceModel\Product::COLUMN_ACCOUNT_ID,
            $accountId
        );

        $productCollection->getSelect()
                          ->reset(\Magento\Framework\DB\Select::COLUMNS)
                          ->columns(\M2E\AmazonMcf\Model\ResourceModel\Product::COLUMN_ID);

        $this->logResource
            ->getConnection()
            ->delete(
                $this->logResource->getMainTable(),
                [
                    \M2E\AmazonMcf\Model\ResourceModel\Product\Log::COLUMN_PRODUCT_ID . ' IN (?)'
                    => $productCollection->getSelect(),
                ]
            );
    }

    public function deleteByProductIds(array $productIds): void
    {
        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addFieldToFilter(
            \M2E\AmazonMcf\Model\ResourceModel\Product::COLUMN_ID,
            ['in' => $productIds]
        );

        $productCollection->getSelect()
                          ->reset(\Magento\Framework\DB\Select::COLUMNS)
                          ->columns(\M2E\AmazonMcf\Model\ResourceModel\Product::COLUMN_ID);

        $this->logResource
            ->getConnection()
            ->delete(
                $this->logResource->getMainTable(),
                [
                    \M2E\AmazonMcf\Model\ResourceModel\Product\Log::COLUMN_PRODUCT_ID . ' IN (?)'
                    => $productCollection->getSelect(),
                ]
            );
    }
}
