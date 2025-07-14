<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\ResourceModel\Product\Grid;

use Magento\Framework\Api\Search\SearchResultInterface;
use M2E\AmazonMcf\Model\ResourceModel\Product as ProductResource;
use M2E\AmazonMcf\Model\ResourceModel\Account as AccountResource;

class Collection extends \Magento\Framework\Data\Collection implements SearchResultInterface
{
    use \M2E\AmazonMcf\Model\ResourceModel\Grid\SearchResultTrait;

    public const PRIMARY_COLUMN = 'product_id';

    private \M2E\AmazonMcf\Model\ResourceModel\Magento\Product\Collection $wrappedCollection;
    private \M2E\AmazonMcf\Model\ResourceModel\Product $productResource;
    private \M2E\AmazonMcf\Model\ResourceModel\Account $accountResource;
    private \M2E\AmazonMcf\Model\Product\Ui\RuntimeStorage $productUiRuntimeStorage;
    private bool $isGetAllItemsFromFilter = false;

    public function __construct(
        \M2E\AmazonMcf\Model\ResourceModel\Magento\Product\CollectionFactory $magentoProductCollectionFactory,
        \M2E\AmazonMcf\Model\ResourceModel\Product $productResource,
        \M2E\AmazonMcf\Model\ResourceModel\Account $accountResource,
        \M2E\AmazonMcf\Model\Product\Ui\RuntimeStorage $productUiRuntimeStorage,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
    ) {
        parent::__construct($entityFactory);

        $this->wrappedCollection = $magentoProductCollectionFactory->create();
        $this->productResource = $productResource;
        $this->accountResource = $accountResource;
        $this->productUiRuntimeStorage = $productUiRuntimeStorage;

        $this->prepareCollection();
    }

    private function prepareCollection(): void
    {
        $this->wrappedCollection->setIdFieldName(self::PRIMARY_COLUMN);
        $this->wrappedCollection->setItemObjectClass(Entity::class);

        $this->wrappedCollection->getSelect()->distinct();
        $this->wrappedCollection->addAttributeToSelect('name');

        $this->wrappedCollection->joinTable(
            ['product' => $this->productResource->getMainTable()],
            sprintf('%s = entity_id', ProductResource::COLUMN_MAGENTO_PRODUCT_ID),
            [
                self::PRIMARY_COLUMN => ProductResource::COLUMN_ID,
                'product_' . ProductResource::COLUMN_ACCOUNT_ID => ProductResource::COLUMN_ACCOUNT_ID,
                'product_' . ProductResource::COLUMN_CHANNEL_SKU => ProductResource::COLUMN_CHANNEL_SKU,
                'product_' . ProductResource::COLUMN_ASIN => ProductResource::COLUMN_ASIN,
                'product_' . ProductResource::COLUMN_MAGENTO_PRODUCT_ID => ProductResource::COLUMN_MAGENTO_PRODUCT_ID,
                'product_' . ProductResource::COLUMN_MAGENTO_PRODUCT_SKU => ProductResource::COLUMN_MAGENTO_PRODUCT_SKU,
                'product_' . ProductResource::COLUMN_QTY => ProductResource::COLUMN_QTY,
                'product_' . ProductResource::COLUMN_IS_ENABLED => ProductResource::COLUMN_IS_ENABLED,
            ]
        );

        $this->wrappedCollection->joinTable(
            ['account' => $this->accountResource->getMainTable()],
            sprintf('%s = product_%s', AccountResource::COLUMN_ID, ProductResource::COLUMN_ACCOUNT_ID),
            [
                'account_' . AccountResource::COLUMN_ID => AccountResource::COLUMN_ID,
                'account_' . AccountResource::COLUMN_MERCHANT_ID => AccountResource::COLUMN_MERCHANT_ID,
            ]
        );
    }

    public function getItems()
    {
        $items = $this->wrappedCollection->getItems();
        $productIds = [];
        foreach ($items as $item) {
            $productIds[] = (int)$item['product_id'];
        }

        if (!$this->isGetAllItemsFromFilter) {
            $this->productUiRuntimeStorage->loadByIds(array_unique($productIds));
        }

        return $items;
    }

    public function addFieldToFilter($field, $condition)
    {
        $this->wrappedCollection->addFieldToFilter($field, $condition);

        return $this;
    }

    public function getSelect()
    {
        return $this->wrappedCollection->getSelect();
    }

    public function setPageSize($size)
    {
        if ($size === false) {
            $this->isGetAllItemsFromFilter = true;
        }

        $this->wrappedCollection->setPageSize($size);

        return $this;
    }

    public function setCurPage($page)
    {
        $this->wrappedCollection->setCurPage($page);

        return $this;
    }

    public function setOrder($field, $direction = \Magento\Framework\Data\Collection::SORT_ORDER_DESC)
    {
        if ($field === 'column_title') {
            $field = 'name';
        }

        $this->wrappedCollection->setOrder($field, $direction);

        return $this;
    }

    public function getTotalCount(): int
    {
        return $this->wrappedCollection->getSize();
    }

    /**
     * Use case: Calling plugins of third-party modules
     */
    public function __call($method, $arguments)
    {
        return $this->wrappedCollection->{$method}(...$arguments);
    }
}
