<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\UI\Order\Log\Column;

class MagentoIncrementId extends \Magento\Ui\Component\Listing\Columns\Column
{
    private \M2E\AmazonMcf\Helper\Magento\Url $magentoUrlHelper;

    public function __construct(
        \M2E\AmazonMcf\Helper\Magento\Url $magentoUrlHelper,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->magentoUrlHelper = $magentoUrlHelper;
    }

    public function prepareDataSource(array $dataSource): array
    {
        if (empty($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach ($dataSource['data']['items'] as &$item) {
            $orderUrl = $this->magentoUrlHelper->getUrlSalesOrderView((int)$item['magento_order_id']);
            $item['magento_order_increment_id'] = sprintf(
                '<a href="%s" target="_blank">%s</a>',
                $orderUrl,
                $item['magento_order_increment_id']
            );
        }

        return $dataSource;
    }
}
