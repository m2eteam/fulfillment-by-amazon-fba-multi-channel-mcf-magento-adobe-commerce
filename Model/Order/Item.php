<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Order;

use M2E\AmazonMcf\Model\ResourceModel\Order\Item as OrderItemResource;

class Item extends \Magento\Framework\Model\AbstractModel
{
    private \M2E\AmazonMcf\Model\Magento\Order\Item\Repository $magentoOrderItemRepository;
    private ?\Magento\Sales\Model\Order\Item $magentoOrderItem = null;

    public function __construct(
        \M2E\AmazonMcf\Model\Magento\Order\Item\Repository $magentoOrderItemRepository,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ?\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        ?\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->magentoOrderItemRepository = $magentoOrderItemRepository;
    }

    protected function _construct(): void
    {
        parent::_construct();
        $this->_init(\M2E\AmazonMcf\Model\ResourceModel\Order\Item::class);
    }

    public function init(
        int $orderId,
        int $productId,
        int $magentoOrderItemId,
        string $sellerFulfillmentItemId,
        string $channelSku,
        int $qty
    ): self {
        $this->setData(OrderItemResource::COLUMN_ORDER_ID, $orderId);
        $this->setData(OrderItemResource::COLUMN_PRODUCT_ID, $productId);
        $this->setData(OrderItemResource::COLUMN_MAGENTO_ORDER_ITEM_ID, $magentoOrderItemId);
        $this->setData(OrderItemResource::COLUMN_SELLER_FULFILLMENT_ITEM_ID, $sellerFulfillmentItemId);
        $this->setData(OrderItemResource::COLUMN_CHANNEL_SKU, $channelSku);
        $this->setData(OrderItemResource::COLUMN_QTY, $qty);

        return $this;
    }

    public function getOrderId(): int
    {
        return (int)$this->getDataByKey(OrderItemResource::COLUMN_ORDER_ID);
    }

    public function getProductId(): int
    {
        return (int)$this->getDataByKey(OrderItemResource::COLUMN_PRODUCT_ID);
    }

    public function getSellerFulfillmentItemId(): string
    {
        return $this->getDataByKey(OrderItemResource::COLUMN_SELLER_FULFILLMENT_ITEM_ID);
    }

    public function getChannelSku(): string
    {
        return $this->getDataByKey(OrderItemResource::COLUMN_CHANNEL_SKU);
    }

    public function getMagentoOrderItemId(): int
    {
        return (int)$this->getDataByKey(OrderItemResource::COLUMN_MAGENTO_ORDER_ITEM_ID);
    }

    public function getQty(): int
    {
        return (int)$this->getDataByKey(OrderItemResource::COLUMN_QTY);
    }

    // ---------------------------------------

    public function isExistsPackageNumber(): bool
    {
        return $this->getDataByKey(OrderItemResource::COLUMN_PACKAGE_NUMBER) !== null;
    }

    public function getPackageNumber(): int
    {
        if (!$this->isExistsPackageNumber()) {
            throw new \LogicException('Package number does not exist');
        }

        return (int)$this->getData(OrderItemResource::COLUMN_PACKAGE_NUMBER);
    }

    public function setPackageNumber(int $packageNumber): self
    {
        $this->setData(OrderItemResource::COLUMN_PACKAGE_NUMBER, $packageNumber);

        return $this;
    }

    // ---------------------------------------

    public function isExistsTrackingNumber(): bool
    {
        return $this->getDataByKey(OrderItemResource::COLUMN_TRACKING_NUMBER) !== null;
    }

    public function getTrackingNumber(): string
    {
        if (!$this->isExistsTrackingNumber()) {
            throw new \LogicException('Tracking number does not exist');
        }

        return $this->getData(OrderItemResource::COLUMN_TRACKING_NUMBER);
    }

    public function setTrackingNumber(string $trackingNumber): self
    {
        $this->setData(OrderItemResource::COLUMN_TRACKING_NUMBER, $trackingNumber);

        return $this;
    }

    // ---------------------------------------

    public function isExistsCarrierCode(): bool
    {
        return $this->getDataByKey(OrderItemResource::COLUMN_CARRIER_CODE) !== null;
    }

    public function getCarrierCode(): string
    {
        if (!$this->isExistsCarrierCode()) {
            throw new \LogicException('Carrier code does not exist');
        }

        return $this->getData(OrderItemResource::COLUMN_CARRIER_CODE);
    }

    public function setCarrierCode(string $carrierCode): self
    {
        $this->setData(OrderItemResource::COLUMN_CARRIER_CODE, $carrierCode);

        return $this;
    }

    // ---------------------------------------

    public function getMagentoOrderItem(): \Magento\Sales\Model\Order\Item
    {
        if ($this->magentoOrderItem !== null) {
            return $this->magentoOrderItem;
        }

        return $this->magentoOrderItem = $this->magentoOrderItemRepository->get(
            $this->getMagentoOrderItemId()
        );
    }
}
