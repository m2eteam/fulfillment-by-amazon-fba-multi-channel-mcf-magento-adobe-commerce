<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\UI\Product\Column;

class Asin extends \Magento\Ui\Component\Listing\Columns\Column
{
    private \M2E\AmazonMcf\Model\Product\Ui\RuntimeStorage $productUiRuntimeStorage;

    public function __construct(
        \M2E\AmazonMcf\Model\Product\Ui\RuntimeStorage $productUiRuntimeStorage,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);

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
            $item['product_asin'] = $product->findAsin() ?? (string)__('N/A');
        }

        return $dataSource;
    }
}
