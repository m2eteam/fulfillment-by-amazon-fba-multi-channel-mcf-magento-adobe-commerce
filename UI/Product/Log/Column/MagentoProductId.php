<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\UI\Product\Log\Column;

class MagentoProductId extends \Magento\Ui\Component\Listing\Columns\Column
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
            if (empty($item['magento_product_id'])) {
                $item['magento_product_id'] = (string)__('N/A');
                continue;
            }

            $magentoProductId = (int)$item['magento_product_id'];
            $url = $this->magentoUrlHelper->getUrlCatalogProductEdit($magentoProductId);
            $item['magento_product_id'] = sprintf('<a href="%s" target="_blank">%d</a>', $url, $magentoProductId);
        }

        return $dataSource;
    }
}
