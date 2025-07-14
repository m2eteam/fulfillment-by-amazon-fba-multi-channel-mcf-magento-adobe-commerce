<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\UI\Order\Column;

class Action extends \Magento\Ui\Component\Listing\Columns\Column
{
    private \Magento\Backend\Model\UrlInterface $urlBuilder;

    public function __construct(
        \Magento\Backend\Model\UrlInterface $urlBuilder,
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
            $logsUrl = $this->urlBuilder->getUrl('m2e_amazonmcf/order_log/index/', ['mcf_order_id' => $item['id']]);
            $item['action'] = sprintf(
                '<a href="%s" target="_blank">%s</a>',
                $logsUrl,
                __('Show Logs')
            );
        }

        return $dataSource;
    }
}
