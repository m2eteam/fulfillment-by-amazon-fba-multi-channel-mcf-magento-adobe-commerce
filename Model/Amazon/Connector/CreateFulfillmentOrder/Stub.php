<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Amazon\Connector\CreateFulfillmentOrder;

class Stub implements BaseInterface
{
    public function process(string $merchantId, Request $request): Response
    {
        throw new \LogicException('Fulfillment order creation process is unavailable');
    }
}
