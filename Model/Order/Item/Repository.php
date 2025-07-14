<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Order\Item;

use M2E\AmazonMcf\Model\ResourceModel\Order\Item as ItemResource;

class Repository
{
    private \M2E\AmazonMcf\Model\ResourceModel\Order\Item $orderItemResource;
    private \M2E\AmazonMcf\Model\ResourceModel\Order\Item\CollectionFactory $orderItemCollectionFactory;
    private \M2E\AmazonMcf\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory;

    public function __construct(
        \M2E\AmazonMcf\Model\ResourceModel\Order\Item $orderItemResource,
        \M2E\AmazonMcf\Model\ResourceModel\Order\Item\CollectionFactory $orderItemCollectionFactory,
        \M2E\AmazonMcf\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
    ) {
        $this->orderItemResource = $orderItemResource;
        $this->orderItemCollectionFactory = $orderItemCollectionFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
    }

    public function create(\M2E\AmazonMcf\Model\Order\Item $orderItem): void
    {
        $this->orderItemResource->save($orderItem);
    }

    public function save(\M2E\AmazonMcf\Model\Order\Item $orderItem): void
    {
        $this->orderItemResource->save($orderItem);
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

        $this->orderItemResource
            ->getConnection()
            ->delete(
                $this->orderItemResource->getMainTable(),
                [
                    \M2E\AmazonMcf\Model\ResourceModel\Order\Item::COLUMN_ORDER_ID . ' IN (?)'
                    => $orderCollection->getSelect(),
                ]
            );
    }

    /**
     * @return \M2E\AmazonMcf\Model\Order\Item[]
     */
    public function retrieveByOrderId(int $orderId): array
    {
        $collection = $this->orderItemCollectionFactory->create();
        $collection->addFilter(ItemResource::COLUMN_ORDER_ID, $orderId);

        return array_values($collection->getItems());
    }
}
