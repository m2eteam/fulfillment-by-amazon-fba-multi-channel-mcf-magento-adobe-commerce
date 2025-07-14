<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Amazon;

use M2E\AmazonMcf\Model\Amazon\RetrievePackages\Package;
use M2E\AmazonMcf\Model\Amazon\Connector\GetFulfillmentOrder\Response;

class RetrievePackages
{
    private Connector\GetFulfillmentOrder $getFulfillmentOrderConnector;

    public function __construct(Connector\GetFulfillmentOrder $getFulfillmentOrderConnector)
    {
        $this->getFulfillmentOrderConnector = $getFulfillmentOrderConnector;
    }

    /**
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\AuthorizationException
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\SystemUnavailableException
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\ThrottlingException
     */
    public function process(string $sellerFulfillmentId, \M2E\AmazonMcf\Model\Account $account): RetrievePackages\Result
    {
        $response = $this->getFulfillmentOrderConnector->process($account->getMerchantId(), $sellerFulfillmentId);
        $result = $this->createResult($response);
        if (!empty($response->getMessages())) {
            return $result->setMessages($response->getMessages());
        }

        $preparedPackages = [];
        foreach ($response->getShipmentItems() as $shipmentItem) {
            $preparedPackages[$shipmentItem->getPackageNumber()][] = $shipmentItem->getSellerFulfillmentItemId();
        }

        foreach ($preparedPackages as $packageNumber => $sellerFulfillmentItemsIds) {
            $result->addPackage(
                new Package(
                    $packageNumber,
                    $sellerFulfillmentItemsIds
                )
            );
        }

        return $result;
    }

    private function createResult(Response $response): RetrievePackages\Result
    {
        if (
            !$response->isOrderStatusExists()
            || $response->getOrderStatus() === Response::ORDER_STATUS_PROCESSING
        ) {
            return RetrievePackages\Result::createWithProcessingStatus();
        }

        if ($response->getOrderStatus() === Response::ORDER_STATUS_COMPLETE) {
            return RetrievePackages\Result::createWithCompleteStatus();
        }

        if ($response->getOrderStatus() === Response::ORDER_STATUS_INVALID) {
            return RetrievePackages\Result::createWithInvalidStatus();
        }

        throw new \LogicException(sprintf('Unexpected response order status: "%s"', $response->getOrderStatus()));
    }
}
