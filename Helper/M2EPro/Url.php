<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Helper\M2EPro;

class Url
{
    private \Magento\Framework\UrlInterface $urlBuilder;

    public function __construct(\Magento\Framework\UrlInterface $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
    }

    public function getAmazonAccountsUrl(): string
    {
        return $this->urlBuilder->getUrl('m2epro/amazon_account');
    }

    public function getEbayOrderUrl(int $orderId): string
    {
        return $this->urlBuilder->getUrl(
            'm2epro/ebay_order/view',
            ['id' => $orderId]
        );
    }

    public function getWalmartOrderUrl(int $orderId): string
    {
        return $this->urlBuilder->getUrl(
            'm2epro/walmart_order/view',
            ['id' => $orderId]
        );
    }
}
