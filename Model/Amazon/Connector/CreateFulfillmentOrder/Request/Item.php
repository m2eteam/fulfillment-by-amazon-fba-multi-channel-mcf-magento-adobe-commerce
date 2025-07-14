<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Amazon\Connector\CreateFulfillmentOrder\Request;

class Item
{
    private string $sellerSku;
    private string $sellerFulfillmentOrderItemId;
    private int $qty;

    public function __construct(string $sellerSku, string $sellerFulfillmentOrderItemId, int $qty)
    {
        $this->sellerSku = $sellerSku;
        $this->sellerFulfillmentOrderItemId = $sellerFulfillmentOrderItemId;
        $this->qty = $qty;
    }

    public function getSellerSku(): string
    {
        return $this->sellerSku;
    }

    public function getSellerFulfillmentOrderItemId(): string
    {
        return $this->sellerFulfillmentOrderItemId;
    }

    public function getQty(): int
    {
        return $this->qty;
    }
}
