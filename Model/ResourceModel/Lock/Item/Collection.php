<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\ResourceModel\Lock\Item;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(
            \M2E\AmazonMcf\Model\Lock\Item::class,
            \M2E\AmazonMcf\Model\ResourceModel\Lock\Item::class
        );
    }
}
