<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Amazon\Connector\GetFulfillmentOrder;

class Response implements \M2E\AmazonMcf\Model\Amazon\Connector\Response\ResponseInterface
{
    use \M2E\AmazonMcf\Model\Amazon\Connector\Message\MessageTrait;

    public const ORDER_STATUS_PROCESSING = 'processing';
    public const ORDER_STATUS_COMPLETE = 'complete';
    public const ORDER_STATUS_INVALID = 'invalid';

    /** @var Response\ShipmentItem[] */
    private array $shipmentItems = [];
    private ?string $orderStatus = null;

    public function addShipmentItem(Response\ShipmentItem $shipmentItem): self
    {
        $this->shipmentItems[] = $shipmentItem;

        return $this;
    }

    /**
     * @return Response\ShipmentItem[]
     */
    public function getShipmentItems(): array
    {
        return $this->shipmentItems;
    }

    public function isOrderStatusExists(): bool
    {
        return $this->orderStatus !== null;
    }

    public function getOrderStatus(): ?string
    {
        return $this->orderStatus;
    }

    public function setOrderStatus(string $orderStatus): void
    {
        if (
            !in_array($orderStatus, [
                self::ORDER_STATUS_COMPLETE,
                self::ORDER_STATUS_PROCESSING,
                self::ORDER_STATUS_INVALID,
            ])
        ) {
            throw new \RuntimeException(sprintf('Unexpected order status: "%s"', $orderStatus));
        }

        $this->orderStatus = $orderStatus;
    }
}
