<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\ResourceModel\Order\Log\Grid;

use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection implements SearchResultInterface
{
    use \M2E\AmazonMcf\Model\ResourceModel\Grid\SearchResultTrait;

    private \M2E\AmazonMcf\Model\ResourceModel\Order $orderResource;

    public function __construct(
        \M2E\AmazonMcf\Model\ResourceModel\Order $orderResource,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        ?\Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        ?\Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);

        $this->orderResource = $orderResource;
        $this->prepareCollection();
    }

    protected function _construct()
    {
        parent::_construct();

        $this->_init(
            \Magento\Framework\View\Element\UiComponent\DataProvider\Document::class,
            \M2E\AmazonMcf\Model\ResourceModel\Order\Log::class,
        );
    }

    private function prepareCollection(): void
    {
        $this->join(
            ['order' => $this->orderResource->getMainTable()],
            sprintf(
                'main_table.%s = order.%s',
                \M2E\AmazonMcf\Model\ResourceModel\Order\Log::COLUMN_ORDER_ID,
                \M2E\AmazonMcf\Model\ResourceModel\Order::COLUMN_ID
            ),
            [
                'mcf_order_id' => sprintf('order.%s', \M2E\AmazonMcf\Model\ResourceModel\Order::COLUMN_ID),
                \M2E\AmazonMcf\Model\ResourceModel\Order::COLUMN_MAGENTO_ORDER_INCREMENT_ID,
                \M2E\AmazonMcf\Model\ResourceModel\Order::COLUMN_MAGENTO_ORDER_ID,
            ]
        );

        $this->addFilterToMap(
            'id',
            sprintf('main_table.%s', \M2E\AmazonMcf\Model\ResourceModel\Order\Log::COLUMN_ID)
        );

        $this->addFilterToMap(
            'mcf_order_id',
            sprintf('order.%s', \M2E\AmazonMcf\Model\ResourceModel\Order::COLUMN_ID),
        );
    }
}
