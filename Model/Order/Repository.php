<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Order;

use M2E\AmazonMcf\Model\ResourceModel\Order as OrderResource;

class Repository
{
    private \M2E\AmazonMcf\Model\ResourceModel\Order $orderResource;
    private \M2E\AmazonMcf\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory;
    private \M2E\AmazonMcf\Model\OrderFactory $orderFactory;

    public function __construct(
        \M2E\AmazonMcf\Model\ResourceModel\Order $orderResource,
        \M2E\AmazonMcf\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \M2E\AmazonMcf\Model\OrderFactory $orderFactory
    ) {
        $this->orderResource = $orderResource;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->orderFactory = $orderFactory;
    }

    public function create(\M2E\AmazonMcf\Model\Order $order): void
    {
        $this->orderResource->save($order);
    }

    public function save(\M2E\AmazonMcf\Model\Order $order): void
    {
        $this->orderResource->save($order);
    }

    public function deleteByAccountId(int $accountId): void
    {
        $this->orderResource
            ->getConnection()
            ->delete(
                $this->orderResource->getMainTable(),
                [OrderResource::COLUMN_ACCOUNT_ID . ' = ?' => $accountId],
            );
    }

    public function get(int $id): \M2E\AmazonMcf\Model\Order
    {
        $order = $this->find($id);
        if ($order === null) {
            throw new \LogicException('Order not found');
        }

        return $order;
    }

    public function find(int $id): ?\M2E\AmazonMcf\Model\Order
    {
        $order = $this->orderFactory->createEmpty();
        $this->orderResource->load($order, $id);

        if ($order->isObjectNew()) {
            return null;
        }

        return $order;
    }

    public function findByChannelId(int $channelOrderId, string $channel): ?\M2E\AmazonMcf\Model\Order
    {
        $collection = $this->orderCollectionFactory->create();
        $collection->addFieldToFilter(OrderResource::COLUMN_CHANNEL_ORDER_ID, $channelOrderId);
        $collection->addFieldToFilter(OrderResource::COLUMN_CHANNEL, $channel);

        /** @var \M2E\AmazonMcf\Model\Order|false $order */
        $order = $collection->fetchItem();
        if ($order === false) {
            return null;
        }

        return $order;
    }

    public function findByMagentoOrderId(int $magentoOrderId): ?\M2E\AmazonMcf\Model\Order
    {
        $order = $this->orderFactory->createEmpty();
        $this->orderResource->load(
            $order,
            $magentoOrderId,
            OrderResource::COLUMN_MAGENTO_ORDER_ID
        );

        if ($order->isObjectNew()) {
            return null;
        }

        return $order;
    }

    public function isExistsWithMagentoOrderId(int $magentoOrderId): bool
    {
        return $this->findByMagentoOrderId($magentoOrderId) !== null;
    }

    /**
     * @return \M2E\AmazonMcf\Model\Order[]
     */
    public function findPaidWithStatusPending(int $limitCount, \DateTime $attemptDatePoint): array
    {
        $collection = $this->orderCollectionFactory->create();
        $collection->addFieldToFilter(
            OrderResource::COLUMN_STATUS,
            \M2E\AmazonMcf\Model\Order::STATUS_PENDING
        );
        $select = $collection->getSelect();
        $select->limit($limitCount);
        $select->where(
            new \Magento\Framework\DB\Sql\Expression(
                sprintf(
                    '%1$s <= "%2$s" OR %1$s IS NULL',
                    OrderResource::COLUMN_STATUS_PROCESS_ATTEMPT_DATE,
                    $attemptDatePoint->format('Y-m-d H:i:s')
                )
            )
        );
        $select->where(OrderResource::COLUMN_IS_PAID, 1);
        $select->order(OrderResource::COLUMN_UPDATE_DATE . ' DESC');

        return array_values($collection->getItems());
    }

    /**
     * @return \M2E\AmazonMcf\Model\Order[]
     */
    public function findWithStatusWaitCreatedPackage(int $limitCount, \DateTime $attemptDatePoint): array
    {
        return $this->findWithStatus(
            \M2E\AmazonMcf\Model\Order::STATUS_WAIT_CREATED_PACKAGE,
            $limitCount,
            $attemptDatePoint
        );
    }

    /**
     * @return \M2E\AmazonMcf\Model\Order[]
     */
    public function findWithStatusWaitShip(int $limitCount, \DateTime $attemptDatePoint): array
    {
        return $this->findWithStatus(
            \M2E\AmazonMcf\Model\Order::STATUS_WAIT_SHIP,
            $limitCount,
            $attemptDatePoint
        );
    }

    /**
     * @return \M2E\AmazonMcf\Model\Order[]
     */
    public function findWithStatusShipped(int $limitCount, \DateTime $attemptDatePoint): array
    {
        return $this->findWithStatus(
            \M2E\AmazonMcf\Model\Order::STATUS_SHIPPED,
            $limitCount,
            $attemptDatePoint
        );
    }

    /**
     * @return \M2E\AmazonMcf\Model\Order[]
     */
    public function findWithStatus(int $status, int $limitCount, \DateTime $attemptDatePoint): array
    {
        $collection = $this->orderCollectionFactory->create();
        $collection->addFieldToFilter(
            OrderResource::COLUMN_STATUS,
            $status
        );
        $collection->getSelect()->limit($limitCount);
        $collection->getSelect()->where(
            new \Magento\Framework\DB\Sql\Expression(
                sprintf(
                    '%1$s <= "%2$s" OR %1$s IS NULL',
                    OrderResource::COLUMN_STATUS_PROCESS_ATTEMPT_DATE,
                    $attemptDatePoint->format('Y-m-d H:i:s')
                )
            )
        );

        $collection->getSelect()->order(OrderResource::COLUMN_UPDATE_DATE . ' DESC');

        return array_values($collection->getItems());
    }

    public function getTotalCount(): int
    {
        return $this->orderCollectionFactory
            ->create()
            ->getSize();
    }

    public function getCountOfShippedAndCompletedByDateRange(\DateTime $dateStart, \DateTime $dateEnd): int
    {
        $collection = $this->orderCollectionFactory->create();
        $collection->addFieldToFilter(
            OrderResource::COLUMN_STATUS,
            [
                \M2E\AmazonMcf\Model\Order::STATUS_SHIPPED,
                \M2E\AmazonMcf\Model\Order::STATUS_COMPLETE,
            ]
        );
        $collection->getSelect()->where(
            sprintf(
                "%s BETWEEN '%s' AND '%s'",
                OrderResource::COLUMN_SHIPPING_DATE,
                $dateStart->format('Y-m-d H:i:s'),
                $dateEnd->format('Y-m-d H:i:s')
            )
        );

        return $collection->getSize();
    }

    public function getCountOfAllShippedAndCompleted(): int
    {
        $collection = $this->orderCollectionFactory->create();
        $collection->addFieldToFilter(
            OrderResource::COLUMN_STATUS,
            [
                \M2E\AmazonMcf\Model\Order::STATUS_SHIPPED,
                \M2E\AmazonMcf\Model\Order::STATUS_COMPLETE,
            ]
        );

        return $collection->getSize();
    }

    public function getCountOfAllUnshipped(): int
    {
        $collection = $this->orderCollectionFactory->create();
        $collection->addFieldToFilter(
            OrderResource::COLUMN_STATUS,
            [
                \M2E\AmazonMcf\Model\Order::STATUS_WAIT_CREATED_PACKAGE,
                \M2E\AmazonMcf\Model\Order::STATUS_WAIT_SHIP,
            ]
        );

        return $collection->getSize();
    }

    public function getCountOfSkipped(): int
    {
        $collection = $this->orderCollectionFactory->create();
        $collection->addFieldToFilter(
            OrderResource::COLUMN_STATUS,
            \M2E\AmazonMcf\Model\Order::STATUS_SKIPPED
        );

        return $collection->getSize();
    }
}
