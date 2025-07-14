<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Amazon\Connector\CreateFulfillmentOrder;

class Request
{
    public const SHIPPING_SPEED_CATEGORY_STANDARD = 'Standard';
    public const SHIPPING_SPEED_CATEGORY_EXPEDITED = 'Expedited';
    public const SHIPPING_SPEED_CATEGORY_PRIORITY = 'Priority';
    public const SHIPPING_SPEED_CATEGORY_SCHEDULED_DELIVERY = 'ScheduledDelivery';

    private static array $allowedSippingSpeedCategories = [
        self::SHIPPING_SPEED_CATEGORY_STANDARD,
        self::SHIPPING_SPEED_CATEGORY_EXPEDITED,
        self::SHIPPING_SPEED_CATEGORY_PRIORITY,
        self::SHIPPING_SPEED_CATEGORY_SCHEDULED_DELIVERY,
    ];

    private string $sellerFulfillmentId;
    private string $displayableOrderId;
    private string $displayableOrderComment;
    private string $shippingSpeedCategory;
    private \DateTime $displayableOrderDate;
    private Request\DestinationAddress $destinationAddress;
    /** @var Request\Item[] */
    private array $items = [];

    public function __construct(
        string $sellerFulfillmentId,
        string $displayableOrderId,
        string $displayableOrderComment,
        string $shippingSpeedCategory,
        \DateTime $displayableOrderDate,
        Request\DestinationAddress $destinationAddress
    ) {
        if (!in_array($shippingSpeedCategory, self::$allowedSippingSpeedCategories)) {
            throw new \LogicException('Invalid shipping speed category');
        }

        $this->sellerFulfillmentId = $sellerFulfillmentId;
        $this->displayableOrderId = $displayableOrderId;
        $this->displayableOrderComment = $displayableOrderComment;
        $this->shippingSpeedCategory = $shippingSpeedCategory;
        $this->displayableOrderDate = $displayableOrderDate;
        $this->destinationAddress = $destinationAddress;
    }

    public function getSellerFulfillmentId(): string
    {
        return $this->sellerFulfillmentId;
    }

    public function getDisplayableOrderId(): string
    {
        return $this->displayableOrderId;
    }

    public function getDestinationAddress(): Request\DestinationAddress
    {
        return $this->destinationAddress;
    }

    public function getDisplayableOrderComment(): string
    {
        return $this->displayableOrderComment;
    }

    public function getShippingSpeedCategory(): string
    {
        return $this->shippingSpeedCategory;
    }

    public function getDisplayableOrderDate(): \DateTime
    {
        return $this->displayableOrderDate;
    }

    public function addItem(Request\Item $item): void
    {
        $this->items[] = $item;
    }

    /**
     * @return Request\Item[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
