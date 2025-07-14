<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Amazon\Connector\GetFulfillmentOrder;

class Stub implements BaseInterface
{
    public function process(string $merchantId, string $sellerFulfillmentId): Response
    {
        throw new \LogicException('Fulfillment order receipt process is unavailable');
    }
}
