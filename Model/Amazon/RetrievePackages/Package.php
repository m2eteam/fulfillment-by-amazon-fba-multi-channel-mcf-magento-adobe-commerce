<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Amazon\RetrievePackages;

class Package
{
    private int $packageNumber;
    /** @var string[] */
    private array $sellerFulfillmentItemIds;

    public function __construct(int $packageNumber, array $sellerFulfillmentItemIds)
    {
        $this->packageNumber = $packageNumber;
        $this->sellerFulfillmentItemIds = $sellerFulfillmentItemIds;
    }

    public function getPackageNumber(): int
    {
        return $this->packageNumber;
    }

    public function getSellerFulfillmentItemIds(): array
    {
        return $this->sellerFulfillmentItemIds;
    }
}
