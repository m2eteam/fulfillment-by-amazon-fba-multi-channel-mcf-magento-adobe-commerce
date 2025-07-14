<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\ResourceModel\Magento\Product;

class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
    private \M2E\Core\Helper\Magento\Staging $stagingHelper;

    public function __construct(
        \M2E\Core\Helper\Magento\Staging $stagingHelper,
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        \Magento\Catalog\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Catalog\Model\Indexer\Product\Flat\State $catalogProductFlatState,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory,
        \Magento\Catalog\Model\ResourceModel\Url $catalogUrl,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Customer\Api\GroupManagementInterface $groupManagement,
        ?\Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        ?\Magento\Catalog\Model\ResourceModel\Product\Collection\ProductLimitationFactory $productLimitationFactory = null,
        ?\Magento\Framework\EntityManager\MetadataPool $metadataPool = null,
        ?\Magento\Catalog\Model\Indexer\Category\Product\TableMaintainer $tableMaintainer = null,
        ?\Magento\Catalog\Model\Indexer\Product\Price\PriceTableResolver $priceTableResolver = null,
        ?\Magento\Framework\Indexer\DimensionFactory $dimensionFactory = null,
        ?\Magento\Catalog\Model\ResourceModel\Category $categoryResourceModel = null,
        ?\Magento\CatalogUrlRewrite\Model\Storage\DbStorage $urlFinder = null,
        ?\Magento\Catalog\Model\Product\Gallery\ReadHandler $productGalleryReadHandler = null,
        ?\Magento\Catalog\Model\ResourceModel\Product\Gallery $mediaGalleryResource = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $eavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $storeManager,
            $moduleManager,
            $catalogProductFlatState,
            $scopeConfig,
            $productOptionFactory,
            $catalogUrl,
            $localeDate,
            $customerSession,
            $dateTime,
            $groupManagement,
            $connection,
            $productLimitationFactory,
            $metadataPool,
            $tableMaintainer,
            $priceTableResolver,
            $dimensionFactory,
            $categoryResourceModel,
            $urlFinder,
            $productGalleryReadHandler,
            $mediaGalleryResource
        );

        $this->stagingHelper = $stagingHelper;
    }

    public function setIdFieldName(string $indexTableColumn): self
    {
        $this->_setIdFieldName($indexTableColumn);

        return $this;
    }

    /**
     * Compatibility with Magento Enterprise (Staging modules) - entity_id column issue
     */
    public function joinTable($table, $bind, $fields = null, $cond = null, $joinType = 'inner')
    {
        $bind = $this->findStagingBind($table, $bind) ?? $bind;

        return parent::joinTable($table, $bind, $fields, $cond, $joinType);
    }

    private function findStagingBind($table, string $bind): ?string
    {
        if (
            $this->stagingHelper->isInstalled()
            && $this->stagingHelper->isStagedTable(
                $table,
                \Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE
            )
            && strpos($bind, 'entity_id') !== false
        ) {
            return str_replace(
                'entity_id',
                $this->stagingHelper->getTableLinkField(
                    \Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE
                ),
                $bind
            );
        }

        return null;
    }
}
