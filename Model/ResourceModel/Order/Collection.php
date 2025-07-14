<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\ResourceModel\Order;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \M2E\AmazonMcf\Model\Order::class,
            \M2E\AmazonMcf\Model\ResourceModel\Order::class
        );
    }
}
