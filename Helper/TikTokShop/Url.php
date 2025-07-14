<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Helper\TikTokShop;

class Url
{
    private \Magento\Framework\UrlInterface $urlBuilder;

    public function __construct(\Magento\Framework\UrlInterface $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
    }

    public function getOrderUrl(int $orderId): string
    {
        return $this->urlBuilder->getUrl(
            'm2e_tiktokshop/tiktokshop_order/view',
            ['id' => $orderId]
        );
    }
}
