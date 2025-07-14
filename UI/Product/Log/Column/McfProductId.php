<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\UI\Product\Log\Column;

class McfProductId extends \Magento\Ui\Component\Listing\Columns\Column
{
    private \Magento\Framework\UrlInterface $urlBuilder;

    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->urlBuilder = $urlBuilder;
    }

    public function prepareDataSource(array $dataSource): array
    {
        if (empty($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach ($dataSource['data']['items'] as &$item) {
            if (empty($item['mcf_product_id'])) {
                $item['mcf_product_id'] = __('N/A');
                continue;
            }

            $mcfProductId = $item['mcf_product_id'];
            $url = $this->urlBuilder->getUrl('m2e_amazonmcf/product/index', ['id' => $mcfProductId]);
            $item['mcf_product_id'] = sprintf(
                '<a href="%s" target="_blank">%s</a>',
                $url,
                $mcfProductId
            );
        }

        return $dataSource;
    }
}
