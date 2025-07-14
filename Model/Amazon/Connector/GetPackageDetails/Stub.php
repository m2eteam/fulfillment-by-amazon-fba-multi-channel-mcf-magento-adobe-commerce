<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Amazon\Connector\GetPackageDetails;

class Stub implements BaseInterface
{
    public function process(string $merchantId, int $packageNumber): Response
    {
        throw new \LogicException('Get Fulfillment Order Package Detail process is not available');
    }
}
