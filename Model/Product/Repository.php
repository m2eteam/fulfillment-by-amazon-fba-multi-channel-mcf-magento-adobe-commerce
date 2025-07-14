<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Product;

use M2E\AmazonMcf\Model\ResourceModel\Product as ProductResource;

class Repository
{
    private \M2E\AmazonMcf\Model\ProductFactory $productFactory;
    private \M2E\AmazonMcf\Model\ResourceModel\Product $productResource;
    private \M2E\AmazonMcf\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory;

    public function __construct(
        \M2E\AmazonMcf\Model\ProductFactory $productFactory,
        \M2E\AmazonMcf\Model\ResourceModel\Product $productResource,
        \M2E\AmazonMcf\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
    ) {
        $this->productFactory = $productFactory;
        $this->productResource = $productResource;
        $this->productCollectionFactory = $productCollectionFactory;
    }

    public function create(\M2E\AmazonMcf\Model\Product $product): void
    {
        $this->productResource->save($product);
    }

    public function save(\M2E\AmazonMcf\Model\Product $product): void
    {
        $this->productResource->save($product);
    }

    /**
     * @param \M2E\AmazonMcf\Model\Product[] $products
     */
    public function bulkCreateOrSave(array $products): void
    {
        $insert = [];
        $update = [];
        foreach ($products as $product) {
            $row = [
                ProductResource::COLUMN_ACCOUNT_ID => $product->getAccountId(),
                ProductResource::COLUMN_CHANNEL_SKU => $product->getChannelSku(),
                ProductResource::COLUMN_ASIN => $product->findAsin(),
                ProductResource::COLUMN_MAGENTO_PRODUCT_ID => $product->getMagentoProductId(),
                ProductResource::COLUMN_MAGENTO_PRODUCT_SKU => $product->getMagentoProductSku(),
                ProductResource::COLUMN_QTY => $product->getQty(),
                ProductResource::COLUMN_QTY_LAST_UPDATE_DATE => $product->getQtyLastUpdateDate()->format('Y-m-d H:i:s'),
                ProductResource::COLUMN_IS_ENABLED => $product->isEnabled(),
            ];

            if (!$product->isObjectNew()) {
                $row[ProductResource::COLUMN_ID] = $product->getId();
                $update[] = $row;
            } else {
                $row[ProductResource::COLUMN_CREATE_DATE] = \M2E\Core\Helper\Date::createCurrentGmt()->format(
                    'Y-m-d H:i:s'
                );
                $insert[] = $row;
            }
        }

        foreach ([$insert, $update] as $pack) {
            foreach (array_chunk($pack, 100) as $chunk) {
                $this->productResource->getConnection()->insertOnDuplicate(
                    $this->productResource->getMainTable(),
                    $chunk,
                    [
                        ProductResource::COLUMN_QTY,
                        ProductResource::COLUMN_QTY_LAST_UPDATE_DATE,
                        ProductResource::COLUMN_MAGENTO_PRODUCT_SKU,
                        ProductResource::COLUMN_ASIN,
                    ]
                );
            }
        }
    }

    public function delete(\M2E\AmazonMcf\Model\Product $product): void
    {
        $this->productResource->delete($product);
    }

    public function deleteByAccountId(int $accountId): void
    {
        $this->productResource
            ->getConnection()
            ->delete(
                $this->productResource->getMainTable(),
                [ProductResource::COLUMN_ACCOUNT_ID . ' = ?' => $accountId],
            );
    }

    public function deleteByIds(array $ids): void
    {
        $this->productResource
            ->getConnection()
            ->delete(
                $this->productResource->getMainTable(),
                [ProductResource::COLUMN_ID . ' IN (?)' => $ids],
            );
    }

    public function get(int $id): \M2E\AmazonMcf\Model\Product
    {
        $product = $this->find($id);

        if ($product === null) {
            throw new \LogicException(sprintf('Product not found by id "%s".', $id));
        }

        return $product;
    }

    public function find(int $id): ?\M2E\AmazonMcf\Model\Product
    {
        $product = $this->productFactory->create();
        $this->productResource->load($product, $id);

        if ($product->isObjectNew()) {
            return null;
        }

        return $product;
    }

    public function findByChannelSku(string $channelSku, int $accountId): ?\M2E\AmazonMcf\Model\Product
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addFieldToFilter(ProductResource::COLUMN_CHANNEL_SKU, $channelSku);
        $collection->addFieldToFilter(ProductResource::COLUMN_ACCOUNT_ID, $accountId);

        /** @var \M2E\AmazonMcf\Model\Product|false $product */
        $product = $collection->fetchItem();
        if ($product === false) {
            return null;
        }

        return $product;
    }

    public function enableByMassActionFilter(\Magento\Ui\Component\MassAction\Filter $filter): void
    {
        $this->enableOrDisableByMassActionFilter(true, $filter);
    }

    public function disableByMassActionFilter(\Magento\Ui\Component\MassAction\Filter $filter): void
    {
        $this->enableOrDisableByMassActionFilter(false, $filter);
    }

    private function enableOrDisableByMassActionFilter(
        bool $isEnabled,
        \Magento\Ui\Component\MassAction\Filter $filter
    ): void {
        $collection = $this->productCollectionFactory->create();
        $filter->getCollection($collection);
        $collection->getSelect()
                   ->reset(\Magento\Framework\DB\Select::COLUMNS)
                   ->columns(ProductResource::COLUMN_ID);

        $this->productResource->getConnection()->update(
            $this->productResource->getMainTable(),
            [ProductResource::COLUMN_IS_ENABLED => (int)$isEnabled],
            $collection->getSelect()->getPart(\Magento\Framework\DB\Select::WHERE)
        );
    }

    /**
     * @return \M2E\AmazonMcf\Model\Product[]
     */
    public function retrieveByIds(array $ids): array
    {
        $collection = $this->productCollectionFactory->create();

        $collection->addFieldToFilter(ProductResource::COLUMN_ID, ['in' => $ids]);

        return array_values($collection->getItems());
    }

    /**
     * @return \M2E\AmazonMcf\Model\Product[]
     */
    public function retrieveByAccountId(int $accountId): array
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addFieldToFilter(ProductResource::COLUMN_ACCOUNT_ID, $accountId);

        return array_values($collection->getItems());
    }

    /**
     * Get enabled products with positive quantity
     * @return \M2E\AmazonMcf\Model\Product[]
     */
    public function findAvailableByMagentoProductId(array $magentoProductIds, int $accountId): array
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addFieldToFilter(ProductResource::COLUMN_MAGENTO_PRODUCT_ID, ['in' => $magentoProductIds]);
        $collection->addFieldToFilter(ProductResource::COLUMN_ACCOUNT_ID, $accountId);
        $collection->addFieldToFilter(ProductResource::COLUMN_IS_ENABLED, 1);
        $collection->addFieldToFilter(ProductResource::COLUMN_QTY, ['gt' => 0]);

        return array_values($collection->getItems());
    }

    /**
     * @return \M2E\AmazonMcf\Model\Product[]
     */
    public function retrieveByMagentoProductsIds(array $magentoProductsIds): array
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addFieldToFilter(ProductResource::COLUMN_MAGENTO_PRODUCT_ID, ['in' => $magentoProductsIds]);

        return array_values($collection->getItems());
    }

    public function getCountByAccountId(int $accountId): int
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addFieldToFilter(ProductResource::COLUMN_ACCOUNT_ID, $accountId);

        return $collection->getSize();
    }

    public function getCountOfAvailableByAccountId(int $accountId): int
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addFieldToFilter(ProductResource::COLUMN_ACCOUNT_ID, $accountId);
        $collection->addFieldToFilter(ProductResource::COLUMN_IS_ENABLED, 1);
        $collection->addFieldToFilter(ProductResource::COLUMN_QTY, ['neq' => 0]);

        return $collection->getSize();
    }

    public function getContOfEnabledByAccountId(int $accountId): int
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addFieldToFilter(ProductResource::COLUMN_ACCOUNT_ID, $accountId);
        $collection->addFieldToFilter(ProductResource::COLUMN_IS_ENABLED, 1);

        return $collection->getSize();
    }
}
