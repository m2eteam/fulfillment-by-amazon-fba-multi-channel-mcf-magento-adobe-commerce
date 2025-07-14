<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\ResourceModel\Order\Grid;

use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use M2E\AmazonMcf\Model\ResourceModel\Order as OrderResource;
use M2E\AmazonMcf\Model\ResourceModel\Account as AccountResource;

class Collection extends AbstractCollection implements SearchResultInterface
{
    use \M2E\AmazonMcf\Model\ResourceModel\Grid\SearchResultTrait;

    private \M2E\AmazonMcf\UI\Order\DataProvider\FilterManager $filterManager;
    private \M2E\AmazonMcf\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory;
    private \Magento\Sales\Model\ResourceModel\Order $magentoOrderResource;
    private \M2E\AmazonMcf\Model\ResourceModel\Account $accountResource;

    public function __construct(
        \M2E\AmazonMcf\UI\Order\DataProvider\FilterManager $filterManager,
        \Magento\Sales\Model\ResourceModel\Order $magentoOrderResource,
        \M2E\AmazonMcf\Model\ResourceModel\Account $accountResource,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        ?\Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        ?\Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);

        $this->filterManager = $filterManager;
        $this->magentoOrderResource = $magentoOrderResource;
        $this->accountResource = $accountResource;

        $this->prepareCollection();
    }

    protected function _construct()
    {
        parent::_construct();
        $this->_init(
            \Magento\Framework\View\Element\UiComponent\DataProvider\Document::class,
            \M2E\AmazonMcf\Model\ResourceModel\Order::class
        );
    }

    private function prepareCollection(): void
    {
        $this->getSelect()
             ->joinLeft(
                 ['acc' => $this->accountResource->getMainTable()],
                 sprintf(
                     'main_table.%s = acc.%s',
                     OrderResource::COLUMN_ACCOUNT_ID,
                     AccountResource::COLUMN_ID
                 ),
                 [
                     'account_id' => sprintf('acc.%s', AccountResource::COLUMN_ID),
                     'merchant_id' => AccountResource::COLUMN_MERCHANT_ID,
                 ]
             );

        $this->addFilterToMap(
            'id',
            sprintf('main_table.%s', \M2E\AmazonMcf\Model\ResourceModel\Order::COLUMN_ID)
        );
    }

    /**
     * @psalm-suppress ParamNameMismatch
     */
    public function addFieldToFilter($field, $condition = null)
    {
        /** @see \M2E\AmazonMcf\UI\Order\DataProvider::addFilterHideSkipped() */
        if (
            $this->filterManager->isDefaultFilter($field)
            && $this->filterManager->isNeedToHideSkipped()
        ) {
            $this->getSelect()->where(
                \M2E\AmazonMcf\Model\ResourceModel\Order::COLUMN_STATUS . ' <> ?',
                \M2E\AmazonMcf\Model\Order::STATUS_SKIPPED
            );
        }

        if ($this->filterManager->isNeedToIgnoreFilter($field)) {
            return $this;
        }

        return parent::addFieldToFilter($field, $condition);
    }
}
