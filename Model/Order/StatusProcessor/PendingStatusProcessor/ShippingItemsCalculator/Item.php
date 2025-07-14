<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Order\StatusProcessor\PendingStatusProcessor\ShippingItemsCalculator;

class Item
{
    private \Magento\Sales\Api\Data\OrderItemInterface $magentoOrderItem;
    private \M2E\AmazonMcf\Model\Product $product;

    public function __construct(
        \Magento\Sales\Api\Data\OrderItemInterface $magentoOrderItem,
        \M2E\AmazonMcf\Model\Product $product
    ) {
        $this->magentoOrderItem = $magentoOrderItem;
        $this->product = $product;
    }

    public function getQty(): int
    {
        return (int)$this->magentoOrderItem->getQtyOrdered();
    }

    public function getChannelSku(): string
    {
        return $this->product->getChannelSku();
    }

    public function getSellerFulfillmentItemId(): string
    {
        return $this->product->getChannelSku();
    }

    public function getMagentoOrderItem(): \Magento\Sales\Api\Data\OrderItemInterface
    {
        return $this->magentoOrderItem;
    }

    public function getProduct(): \M2E\AmazonMcf\Model\Product
    {
        return $this->product;
    }
}
