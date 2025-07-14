<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Lock\Item;

use M2E\AmazonMcf\Model\ResourceModel\Lock\Item as LockItemResource;

class Repository
{
    private \M2E\AmazonMcf\Model\Lock\ItemFactory $lockItemFactory;
    private \M2E\AmazonMcf\Model\ResourceModel\Lock\Item $lockItemResource;

    public function __construct(
        \M2E\AmazonMcf\Model\Lock\ItemFactory $lockItemFactory,
        \M2E\AmazonMcf\Model\ResourceModel\Lock\Item $lockItemResource
    ) {
        $this->lockItemFactory = $lockItemFactory;
        $this->lockItemResource = $lockItemResource;
    }

    public function find(int $id): ?\M2E\AmazonMcf\Model\Lock\Item
    {
        $lockItem = $this->lockItemFactory->create();
        $this->lockItemResource->load(
            $lockItem,
            $id,
            LockItemResource::COLUMN_ID
        );

        if ($lockItem->isObjectNew()) {
            return null;
        }

        return $lockItem;
    }

    public function findByNick(string $nick): ?\M2E\AmazonMcf\Model\Lock\Item
    {
        $lockItem = $this->lockItemFactory->create();
        $this->lockItemResource->load(
            $lockItem,
            $nick,
            LockItemResource::COLUMN_NICK
        );

        if ($lockItem->isObjectNew()) {
            return null;
        }

        return $lockItem;
    }
}
