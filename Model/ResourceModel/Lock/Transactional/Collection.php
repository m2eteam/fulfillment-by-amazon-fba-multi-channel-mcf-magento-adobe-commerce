<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\ResourceModel\Lock\Transactional;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    public function _construct()
    {
        $this->_init(
            \M2E\AmazonMcf\Model\Lock\Transactional::class,
            \M2E\AmazonMcf\Model\ResourceModel\Lock\Transactional::class
        );
    }
}
