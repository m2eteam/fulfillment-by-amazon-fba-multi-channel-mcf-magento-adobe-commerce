<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\UI\Product\Column;

class Action extends \Magento\Ui\Component\Listing\Columns\Column
{
    private \Magento\Backend\Model\UrlInterface $urlBuilder;
    private \M2E\AmazonMcf\Model\Product\Ui\RuntimeStorage $productUiRuntimeStorage;

    public function __construct(
        \Magento\Backend\Model\UrlInterface $urlBuilder,
        \M2E\AmazonMcf\Model\Product\Ui\RuntimeStorage $productUiRuntimeStorage,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->urlBuilder = $urlBuilder;
        $this->productUiRuntimeStorage = $productUiRuntimeStorage;
    }

    public function prepareDataSource(array $dataSource): array
    {
        if (empty($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach ($dataSource['data']['items'] as &$item) {
            $product = $this->productUiRuntimeStorage->findProduct((int)$item['product_id']);
            if (empty($product)) {
                continue;
            }

            $logsUrl = $this->urlBuilder->getUrl(
                'm2e_amazonmcf/product_log/index/',
                ['mcf_product_id' => $product->getId()]
            );
            $item['action'] = sprintf(
                '<a href="%s" target="_blank">%s</a>',
                $logsUrl,
                __('Show Logs')
            );
        }

        return $dataSource;
    }
}
