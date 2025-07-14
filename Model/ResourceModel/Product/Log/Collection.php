<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\ResourceModel\Product\Log;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \M2E\AmazonMcf\Model\Product\Log::class,
            \M2E\AmazonMcf\Model\ResourceModel\Product\Log::class
        );
    }
}
