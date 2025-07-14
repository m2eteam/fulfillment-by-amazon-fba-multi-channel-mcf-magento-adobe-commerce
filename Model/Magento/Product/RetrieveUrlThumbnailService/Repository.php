<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Magento\Product\RetrieveUrlThumbnailService;

class Repository
{
    private \Magento\Framework\App\ResourceConnection $resourceModel;
    private \M2E\Core\Helper\Module\Database\Structure $dbStructureHelper;
    private \Magento\Catalog\Model\ResourceModel\Product $magentoProductResourceModel;
    private \M2E\AmazonMcf\Model\ResourceModel\Magento\Product\CollectionFactory $magentoProductCollectionFactory;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceModel,
        \M2E\Core\Helper\Module\Database\Structure $dbStructureHelper,
        \Magento\Catalog\Model\ResourceModel\Product $magentoProductResourceModel,
        \M2E\AmazonMcf\Model\ResourceModel\Magento\Product\CollectionFactory $magentoProductCollectionFactory
    ) {
        $this->resourceModel = $resourceModel;
        $this->dbStructureHelper = $dbStructureHelper;
        $this->magentoProductResourceModel = $magentoProductResourceModel;
        $this->magentoProductCollectionFactory = $magentoProductCollectionFactory;
    }

    public function getProductThumbnailAttributeId(): int
    {
        $select = $this->resourceModel->getConnection()->select();

        $select->from(
            $this->dbStructureHelper->getTableNameWithPrefix(
                'eav_attribute',
            ),
            ['attribute_id'],
        );
        $select->where('attribute_code = ?', 'thumbnail');
        $select->where(
            'entity_type_id = ?',
            $this->magentoProductResourceModel->getTypeId(),
        );

        $attributeId = $select->query()->fetchColumn();

        return (int)$attributeId;
    }

    public function findProductThumbnailPath(int $magentoProductId, int $attributeId, int $storeId): ?string
    {
        $collection = $this->magentoProductCollectionFactory->create();
        $collection->addFieldToFilter('entity_id', $magentoProductId);
        $collection->joinTable(
            [
                'cpev' => $this->dbStructureHelper->getTableNameWithPrefix('catalog_product_entity_varchar'),
            ],
            'entity_id = entity_id',
            ['value' => 'value'],
        );

        $query = $collection->getSelect()
                            ->reset(\Magento\Framework\DB\Select::COLUMNS)
                            ->columns(['value' => 'cpev.value'])
                            ->where('cpev.store_id = ?', $storeId)
                            ->where('cpev.attribute_id = ?', $attributeId)
                            ->order('cpev.store_id DESC')
                            ->query();

        $thumbnailPath = null;
        while ($tempPath = $query->fetchColumn()) {
            if (
                $tempPath != ''
                && $tempPath != 'no_selection'
                && $tempPath != '/'
            ) {
                $thumbnailPath = $tempPath;
                break;
            }
        }

        return $thumbnailPath;
    }
}
