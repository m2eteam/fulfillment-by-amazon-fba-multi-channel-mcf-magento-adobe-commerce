<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Lock;

use M2E\AmazonMcf\Model\ResourceModel\Lock\Transactional as ResourceLockTransactional;

class Transactional extends \Magento\Framework\Model\AbstractModel
{
    public function _construct()
    {
        parent::_construct();
        $this->_init(\M2E\AmazonMcf\Model\ResourceModel\Lock\Transactional::class);
    }

    public function init(string $nick): self
    {
        $this->setData(ResourceLockTransactional::COLUMN_NICK, $nick);

        return $this;
    }

    public function getNick(): string
    {
        return $this->getData(ResourceLockTransactional::COLUMN_NICK);
    }
}
