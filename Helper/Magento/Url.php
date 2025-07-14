<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Helper\Magento;

class Url
{
    private \Magento\Framework\UrlInterface $urlBuilder;

    public function __construct(\Magento\Framework\UrlInterface $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
    }

    public function getUrlSalesOrderView(int $orderId): string
    {
        return $this->urlBuilder->getUrl(
            'sales/order/view',
            ['order_id' => $orderId]
        );
    }

    public function getUrlCatalogProductEdit(int $productId): string
    {
        return $this->urlBuilder->getUrl(
            'catalog/product/edit',
            [
                'id' => $productId,
                'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
            ]
        );
    }
}
