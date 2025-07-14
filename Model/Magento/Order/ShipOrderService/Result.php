<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Magento\Order\ShipOrderService;

class Result
{
    /** @var \Magento\Sales\Api\Data\ShipmentInterface[] */
    private array $createdShipments = [];
    /** @var string[] */
    private array $messages = [];

    public function addCreatedShipment(\Magento\Sales\Api\Data\ShipmentInterface $shipment): void
    {
        $this->createdShipments[] = $shipment;
    }

    /**
     * @return \Magento\Sales\Api\Data\ShipmentInterface[]
     */
    public function getCreatedShipments(): array
    {
        return $this->createdShipments;
    }

    public function addMessage(string $message): void
    {
        $this->messages[] = $message;
    }

    /**
     * @return string[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }
}
