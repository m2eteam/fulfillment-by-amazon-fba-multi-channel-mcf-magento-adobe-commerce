<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Helper\Kaufland;

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
            'm2e_kaufland/Kaufland_order/view',
            ['id' => $orderId]
        );
    }
}
