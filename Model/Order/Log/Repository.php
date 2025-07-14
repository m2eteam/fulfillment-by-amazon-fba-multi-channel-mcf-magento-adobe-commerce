<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Order\Log;

class Repository
{
    private \M2E\AmazonMcf\Model\ResourceModel\Order\Log $logResource;
    private \M2E\AmazonMcf\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory;

    public function __construct(
        \M2E\AmazonMcf\Model\ResourceModel\Order\Log $logResource,
        \M2E\AmazonMcf\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
    ) {
        $this->logResource = $logResource;
        $this->orderCollectionFactory = $orderCollectionFactory;
    }

    public function create(\M2E\AmazonMcf\Model\Order\Log $log): void
    {
        $this->logResource->save($log);
    }

    public function deleteByAccountId(int $accountId): void
    {
        $orderCollection = $this->orderCollectionFactory->create();
        $orderCollection->addFieldToFilter(
            \M2E\AmazonMcf\Model\ResourceModel\Order::COLUMN_ACCOUNT_ID,
            $accountId
        );

        $orderCollection->getSelect()
                          ->reset(\Magento\Framework\DB\Select::COLUMNS)
                          ->columns(\M2E\AmazonMcf\Model\ResourceModel\Order::COLUMN_ID);

        $this->logResource
            ->getConnection()
            ->delete(
                $this->logResource->getMainTable(),
                [
                    \M2E\AmazonMcf\Model\ResourceModel\Order\Log::COLUMN_ORDER_ID . ' IN (?)'
                    => $orderCollection->getSelect(),
                ]
            );
    }
}
