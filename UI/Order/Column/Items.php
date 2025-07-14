<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\UI\Order\Column;

class Items extends \Magento\Ui\Component\Listing\Columns\Column
{
    private \M2E\AmazonMcf\Model\Order\Item\Repository $orderItemRepository;
    private \M2E\AmazonMcf\Helper\Magento\Url $magentoUrlHelper;

    public function __construct(
        \M2E\AmazonMcf\Helper\Magento\Url $magentoUrlHelper,
        \M2E\AmazonMcf\Model\Order\Item\Repository $orderItemRepository,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->orderItemRepository = $orderItemRepository;
        $this->magentoUrlHelper = $magentoUrlHelper;
    }

    public function prepareDataSource(array $dataSource): array
    {
        if (empty($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach ($dataSource['data']['items'] as &$item) {
            $item['items'] = $this->retrieveOrderItems((int)$item['id']);
        }

        return $dataSource;
    }

    public function retrieveOrderItems(int $orderId): string
    {
        $items = $this->orderItemRepository->retrieveByOrderId($orderId);
        if (empty($items)) {
            return (string)__('N/A');
        }

        $html = '';
        foreach ($items as $item) {
            if ($html != '') {
                $html .= '<br/>';
            }

            $productUrl = $this->magentoUrlHelper->getUrlCatalogProductEdit(
                (int)$item->getMagentoOrderItem()->getProductId()
            );

            $sku = sprintf(
                '<a href="%s" target="_blank">%s</a>',
                $productUrl,
                $item->getMagentoOrderItem()->getSku()
            );

            $html .= sprintf(
                '<span style="padding-left: 10px;"><b>%s:</b>&nbsp;%s</span><br/>',
                __('Sku'),
                $sku
            );

            $html .= sprintf(
                '<span style="padding-left: 10px;"><b>%s:</b>&nbsp;%s</span><br/>',
                __('Amazon Sku'),
                $item->getChannelSku()
            );

            $html .= sprintf(
                '<span style="padding-left: 10px;"><b>%s:</b>&nbsp;%d</span><br/>',
                __('QTY'),
                $item->getQty()
            );

            if ($item->isExistsPackageNumber()) {
                $html .= sprintf(
                    '<span style="padding-left: 10px;"><b>%s:</b>&nbsp;%d</span><br/>',
                    __('Package Number'),
                    $item->getPackageNumber()
                );
            }

            if ($item->isExistsTrackingNumber()) {
                $html .= sprintf(
                    '<span style="padding-left: 10px;"><b>%s:</b>&nbsp;%s</span><br/>',
                    __('Tracking Number'),
                    $item->getTrackingNumber()
                );
            }

            if ($item->isExistsCarrierCode()) {
                $html .= sprintf(
                    '<span style="padding-left: 10px;"><b>%s:</b>&nbsp;%s</span><br/>',
                    __('Carrier Code'),
                    $item->getCarrierCode()
                );
            }
        }

        return $html;
    }
}
