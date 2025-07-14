<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Amazon\Connector\GetFulfillmentOrder\Response;

class ShipmentItem
{
    private string $sellerFulfillmentItemId;
    private int $packageNumber;

    public function __construct(string $sellerFulfillmentItemId, int $packageNumber)
    {
        $this->sellerFulfillmentItemId = $sellerFulfillmentItemId;
        $this->packageNumber = $packageNumber;
    }

    public function getSellerFulfillmentItemId(): string
    {
        return $this->sellerFulfillmentItemId;
    }

    public function getPackageNumber(): int
    {
        return $this->packageNumber;
    }
}
