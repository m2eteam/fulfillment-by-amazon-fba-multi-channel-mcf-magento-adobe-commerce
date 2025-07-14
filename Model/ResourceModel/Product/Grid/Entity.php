<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\ResourceModel\Product\Grid;

class Entity extends \Magento\Framework\View\Element\UiComponent\DataProvider\Document
{
    public function getIdFieldName(): string
    {
        return 'entity_id';
    }

    public function isVisibleInSiteVisibility()
    {
        return false;
    }

    public function getProductId(): int
    {
        return (int)$this->getData(Collection::PRIMARY_COLUMN);
    }
}
