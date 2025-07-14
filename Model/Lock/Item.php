<?php

namespace M2E\AmazonMcf\Model\Lock;

use M2E\AmazonMcf\Model\ResourceModel\Lock\Item as LockItemResource;

class Item extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct(): void
    {
        parent::_construct();
        $this->_init(\M2E\AmazonMcf\Model\ResourceModel\Lock\Item::class);
    }

    public function setNick(string $nick): self
    {
        $this->setData(LockItemResource::COLUMN_NICK, $nick);

        return $this;
    }

    public function getNick(): string
    {
        return $this->getData(LockItemResource::COLUMN_NICK);
    }

    public function setParentId($id): self
    {
        $this->setData('parent_id', $id);

        return $this;
    }

    public function getParentId()
    {
        return $this->getData('parent_id');
    }

    public function getContentData()
    {
        return $this->getData('data');
    }

    //----------------------------------------

    public function getUpdateDate()
    {
        return $this->getData('update_date');
    }

    public function getCreateDate()
    {
        return $this->getData('create_date');
    }
}
