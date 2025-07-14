<?php

namespace M2E\AmazonMcf\Model\Amazon\Connector\GetPackageDetails;

interface BaseInterface
{
    /**
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\AuthorizationException
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\SystemUnavailableException
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\ThrottlingException
     */
    public function process(string $merchantId, int $packageNumber): Response;
}
