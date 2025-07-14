<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Amazon\Connector\GetFulfillmentOrder;

interface BaseInterface
{
    /**
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\AuthorizationException
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\SystemUnavailableException
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\ThrottlingException
     */
    public function process(string $merchantId, string $sellerFulfillmentId): Response;
}
