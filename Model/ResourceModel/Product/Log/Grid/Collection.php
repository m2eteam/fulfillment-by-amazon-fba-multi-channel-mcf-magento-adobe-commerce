<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\ResourceModel\Product\Log\Grid;

use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection implements SearchResultInterface
{
    use \M2E\AmazonMcf\Model\ResourceModel\Grid\SearchResultTrait;

    private \M2E\AmazonMcf\Model\ResourceModel\Product $productResource;

    public function __construct(
        \M2E\AmazonMcf\Model\ResourceModel\Product $productResource,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        ?\Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        ?\Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);

        $this->productResource = $productResource;
        $this->prepareCollection();
    }

    protected function _construct()
    {
        parent::_construct();

        $this->_init(
            \Magento\Framework\View\Element\UiComponent\DataProvider\Document::class,
            \M2E\AmazonMcf\Model\ResourceModel\Product\Log::class,
        );
    }

    private function prepareCollection(): void
    {
        $this->getSelect()->joinLeft(
            ['p' => $this->productResource->getMainTable()],
            sprintf(
                'main_table.%s = p.%s',
                \M2E\AmazonMcf\Model\ResourceModel\Product\Log::COLUMN_PRODUCT_ID,
                \M2E\AmazonMcf\Model\ResourceModel\Product::COLUMN_ID
            ),
            [
                'mcf_product_id' => sprintf('p.%s', \M2E\AmazonMcf\Model\ResourceModel\Product::COLUMN_ID),
                \M2E\AmazonMcf\Model\ResourceModel\Product::COLUMN_MAGENTO_PRODUCT_ID,
            ]
        );

        $this->addFilterToMap(
            'id',
            sprintf('main_table.%s', \M2E\AmazonMcf\Model\ResourceModel\Product\Log::COLUMN_ID)
        );

        $this->addFilterToMap(
            'mcf_product_id',
            sprintf('p.%s', \M2E\AmazonMcf\Model\ResourceModel\Product::COLUMN_ID),
        );
    }
}
