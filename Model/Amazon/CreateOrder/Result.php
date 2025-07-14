<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Amazon\CreateOrder;

class Result
{
    use \M2E\AmazonMcf\Model\Amazon\Connector\Message\MessageTrait;

    private string $sellerFulfillmentId;

    public function __construct(string $sellerFulfillmentId)
    {
        $this->sellerFulfillmentId = $sellerFulfillmentId;
    }

    public function getSellerFulfillmentId(): string
    {
        return $this->sellerFulfillmentId;
    }
}
