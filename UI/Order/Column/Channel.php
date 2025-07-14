<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\UI\Order\Column;

class Channel extends \Magento\Ui\Component\Listing\Columns\Column
{
    private \M2E\AmazonMcf\Helper\M2EPro\Url $m2eproUrlHelper;
    private \M2E\AmazonMcf\Helper\TikTokShop\Url $ttsUrlHelper;
    private \M2E\AmazonMcf\Helper\Kaufland\Url $kauflandUrlHelper;
    private \M2E\AmazonMcf\Helper\OnBuy\Url $onBuyUrlHelper;
    private \M2E\AmazonMcf\Helper\Otto\Url $ottoUrlHelper;
    private \M2E\AmazonMcf\Helper\Temu\Url $temuUrlHelper;

    public function __construct(
        \M2E\AmazonMcf\Helper\M2EPro\Url $m2eproUrlHelper,
        \M2E\AmazonMcf\Helper\TikTokShop\Url $ttsUrlHelper,
        \M2E\AmazonMcf\Helper\Kaufland\Url $kauflandUrlHelper,
        \M2E\AmazonMcf\Helper\OnBuy\Url $onBuyUrlHelper,
        \M2E\AmazonMcf\Helper\Otto\Url $ottoUrlHelper,
        \M2E\AmazonMcf\Helper\Temu\Url $temuUrlHelper,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->m2eproUrlHelper = $m2eproUrlHelper;
        $this->ttsUrlHelper = $ttsUrlHelper;
        $this->kauflandUrlHelper = $kauflandUrlHelper;
        $this->onBuyUrlHelper = $onBuyUrlHelper;
        $this->ottoUrlHelper = $ottoUrlHelper;
        $this->temuUrlHelper = $temuUrlHelper;
    }

    public function prepareDataSource(array $dataSource): array
    {
        if (empty($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach ($dataSource['data']['items'] as &$item) {
            $item['channel'] = $this->getChannel(
                $item[\M2E\AmazonMcf\Model\ResourceModel\Order::COLUMN_CHANNEL],
                (int)$item[\M2E\AmazonMcf\Model\ResourceModel\Order::COLUMN_CHANNEL_ORDER_ID]
            );
        }

        return $dataSource;
    }

    private function getChannel(string $sourceVal, int $channelOrderId): string
    {
        if ($sourceVal === \M2E\AmazonMcf\Model\Order::CHANNEL_MAGENTO) {
            return (string)__('Magento');
        }

        if ($sourceVal === \M2E\AmazonMcf\Model\Order::CHANNEL_EBAY) {
            return sprintf(
                '<a href="%s" target="_blank">%s<a>',
                $this->m2eproUrlHelper->getEbayOrderUrl($channelOrderId),
                __('eBay'),
            );
        }

        if ($sourceVal === \M2E\AmazonMcf\Model\Order::CHANNEL_WALMART) {
            return sprintf(
                '<a href="%s" target="_blank">%s<a>',
                $this->m2eproUrlHelper->getWalmartOrderUrl($channelOrderId),
                __('Walmart'),
            );
        }

        if ($sourceVal === \M2E\AmazonMcf\Model\Order::CHANNEL_TIKTOK_SHOP) {
            return sprintf(
                '<a href="%s" target="_blank">%s<a>',
                $this->ttsUrlHelper->getOrderUrl($channelOrderId),
                __('TikTok Shop'),
            );
        }

        if ($sourceVal === \M2E\AmazonMcf\Model\Order::CHANNEL_KAUFLAND) {
            return sprintf(
                '<a href="%s" target="_blank">%s<a>',
                $this->kauflandUrlHelper->getOrderUrl($channelOrderId),
                __('Kaufland'),
            );
        }

        if ($sourceVal === \M2E\AmazonMcf\Model\Order::CHANNEL_ONBUY) {
            return sprintf(
                '<a href="%s" target="_blank">%s<a>',
                $this->onBuyUrlHelper->getOrderUrl($channelOrderId),
                __('OnBuy'),
            );
        }

        if ($sourceVal === \M2E\AmazonMcf\Model\Order::CHANNEL_OTTO) {
            return sprintf(
                '<a href="%s" target="_blank">%s<a>',
                $this->ottoUrlHelper->getOrderUrl($channelOrderId),
                __('Otto'),
            );
        }

        if ($sourceVal === \M2E\AmazonMcf\Model\Order::CHANNEL_TEMU) {
            return sprintf(
                '<a href="%s" target="_blank">%s<a>',
                $this->temuUrlHelper->getOrderUrl($channelOrderId),
                __('Temu'),
            );
        }

        return (string)__('N/A');
    }
}
