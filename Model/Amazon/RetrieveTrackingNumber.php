<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Amazon;

class RetrieveTrackingNumber
{
    private Connector\GetPackagesDetails $getPackageTrackingDetailsConnector;

    public function __construct(Connector\GetPackagesDetails $getPackageTrackingDetailsConnector)
    {
        $this->getPackageTrackingDetailsConnector = $getPackageTrackingDetailsConnector;
    }

    /**
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\AuthorizationException
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\SystemUnavailableException
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\ThrottlingException
     */
    public function process(int $packageNumber, \M2E\AmazonMcf\Model\Account $account): RetrieveTrackingNumber\Result
    {
        $response = $this->getPackageTrackingDetailsConnector->process(
            $account->getMerchantId(),
            $packageNumber
        );

        $result = new RetrieveTrackingNumber\Result();

        if (!empty($response->getMessages())) {
            return $result->setMessages($response->getMessages());
        }

        if ($trackingNumber = $response->retrieveTrackingNumber()) {
            $result->setTrackingNumber($trackingNumber);
        }

        if ($carrierCode = $response->retrieveCarrierCode()) {
            $result->setCarrierCode($carrierCode);
        }

        if ($carrierUrl = $response->retrieveCarrierUrl()) {
            $result->setCarrierUrl($carrierUrl);
        }

        return $result;
    }
}
