<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Magento\Order\ShipOrderService;

class ShipmentItem
{
    private int $qty;
    private int $magentoOrderItemId;
    private string $trackingNumber;
    private ?string $carrierCode;

    public function __construct(
        int $magentoOrderItemId,
        int $qty,
        string $trackingNumber,
        ?string $carrierCode
    ) {
        $this->magentoOrderItemId = $magentoOrderItemId;
        $this->qty = $qty;
        $this->trackingNumber = $trackingNumber;
        $this->carrierCode = $carrierCode;
    }

    public function getMagentoOrderItemId(): int
    {
        return $this->magentoOrderItemId;
    }

    public function getTrackingNumber(): string
    {
        return $this->trackingNumber;
    }

    public function getQty(): int
    {
        return $this->qty;
    }

    public function getCarrierCode(): ?string
    {
        return $this->carrierCode;
    }
}
