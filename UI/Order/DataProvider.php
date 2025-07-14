<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\UI\Order;

class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    public function __construct(
        DataProvider\FilterManager $filterManager,
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Framework\Api\Search\ReportingInterface $reporting,
        \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );

        $this->addFilterHideSkipped($filterManager);
    }

    private function addFilterHideSkipped(DataProvider\FilterManager $filterManager): void
    {
        if (!$this->request->getParam('isAjax')) {
            return;
        }

        $filterManager->decideToHideSkipped($this->request);
        $defaultFilter = $filterManager->getDefaultFilter($this->filterBuilder);

        /**
         * Default filter needed for mandatory call addFieldToFilter() in resource collection
         * @see \M2E\AmazonMcf\Model\ResourceModel\Order\Grid\Collection::addFieldToFilter()
         */
        $this->addFilter($defaultFilter);
    }
}
