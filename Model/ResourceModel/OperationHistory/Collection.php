<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\ResourceModel\OperationHistory;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    public function _construct()
    {
        $this->_init(
            \M2E\AmazonMcf\Model\OperationHistory::class,
            \M2E\AmazonMcf\Model\ResourceModel\OperationHistory::class
        );
    }
}
