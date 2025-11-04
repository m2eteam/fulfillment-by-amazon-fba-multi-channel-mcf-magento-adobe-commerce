<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Order\StatusProcessor;

class WaitShipStatusProcessor
{
    private \M2E\AmazonMcf\Model\Amazon\RetrieveTrackingNumber $retrieveTrackingNumber;
    private \M2E\AmazonMcf\Model\Order\Repository $orderRepository;
    private \M2E\AmazonMcf\Model\Order\Item\Repository $orderItemRepository;
    private \M2E\AmazonMcf\Model\Order\Log\Logger $orderLogger;

    public function __construct(
        \M2E\AmazonMcf\Model\Amazon\RetrieveTrackingNumber $retrieveTrackingNumber,
        \M2E\AmazonMcf\Model\Order\Repository $orderRepository,
        \M2E\AmazonMcf\Model\Order\Item\Repository $orderItemRepository,
        \M2E\AmazonMcf\Model\Order\Log\Logger $orderLogger
    ) {
        $this->retrieveTrackingNumber = $retrieveTrackingNumber;
        $this->orderRepository = $orderRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->orderLogger = $orderLogger;
    }

    /**
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\AuthorizationException
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\SystemUnavailableException
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\ThrottlingException
     */
    public function process(\M2E\AmazonMcf\Model\Order $order): void
    {
        if (!$order->isWaitShipStatus()) {
            return;
        }

        $this->saveTrackingNumbersToOrderItems($order);
        if (!$this->isAllOrderItemsWithTrackingNumber($order->getItems())) {
            $this->skipOrder(
                (string)__('Order was not updated: unable to retrieve tracking number from Amazon.'),
                $order
            );

            return;
        }

        $this->saveOrderAsShipped($order);
    }

    /**
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\AuthorizationException
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\SystemUnavailableException
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\ThrottlingException
     */
    private function saveTrackingNumbersToOrderItems(\M2E\AmazonMcf\Model\Order $order): void
    {
        /** @var list<string, \M2E\AmazonMcf\Model\Amazon\RetrieveTrackingNumber\Result> $foundTrackingData */
        $foundTrackingData = [];
        foreach ($order->getItems() as $orderItem) {
            if ($orderItem->isExistsTrackingNumber()) {
                continue;
            }

            if (isset($foundTrackingData[$orderItem->getPackageNumber()])) {
                $this->saveOrderItem($orderItem, $foundTrackingData[$orderItem->getPackageNumber()]);
                continue;
            }

            $trackingData = $this->findTrackingDataOnAmazon($orderItem->getPackageNumber(), $order);
            if ($trackingData === null) {
                continue;
            }

            $this->saveOrderItem($orderItem, $trackingData);
            $foundTrackingData[$orderItem->getPackageNumber()] = $trackingData;
        }
    }

    private function saveOrderItem(
        \M2E\AmazonMcf\Model\Order\Item $orderItem,
        \M2E\AmazonMcf\Model\Amazon\RetrieveTrackingNumber\Result $trackingData
    ): void {
        $orderItem->setTrackingNumber($trackingData->retrieveTrackingNumber());
        if ($carrierCode = $trackingData->retrieveCarrierCode()) {
            $orderItem->setCarrierCode($carrierCode);
        }
        if ($carrierUrl = $trackingData->retrieveCarrierUrl()) {
            $orderItem->setCarrierUrl($carrierUrl);
        }

        $this->orderItemRepository->save($orderItem);
    }

    /**
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\AuthorizationException
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\SystemUnavailableException
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\ThrottlingException
     */
    private function findTrackingDataOnAmazon(
        int $packageNumber,
        \M2E\AmazonMcf\Model\Order $order
    ): ?\M2E\AmazonMcf\Model\Amazon\RetrieveTrackingNumber\Result {
        $result = $this->retrieveTrackingNumber->process(
            $packageNumber,
            $order->getAccount()
        );

        if (!empty($result->getMessages())) {
            $this->addLogByMessages($result->getMessages(), $order);

            return null;
        }

        if ($result->retrieveTrackingNumber() === null) {
            $this->skipOrder(
                (string)__('Order update failed: Tracking Number was not provided by Amazon'),
                $order
            );

            return null;
        }

        return $result;
    }

    /**
     * @param \M2E\AmazonMcf\Model\Amazon\Connector\Message\Message[] $messages
     */
    private function addLogByMessages(array $messages, \M2E\AmazonMcf\Model\Order $order): void
    {
        $texts = array_map(function (\M2E\AmazonMcf\Model\Amazon\Connector\Message\Message $message) {
            return $message->getText();
        }, $messages);

        $this->orderLogger->warning(implode(' ', $texts), $order->getId());
    }

    /**
     * @param \M2E\AmazonMcf\Model\Order\Item[] $orderItems
     */
    private function isAllOrderItemsWithTrackingNumber(array $orderItems): bool
    {
        if (empty($orderItems)) {
            return false;
        }

        foreach ($orderItems as $orderItem) {
            if (!$orderItem->isExistsTrackingNumber()) {
                return false;
            }
        }

        return true;
    }

    private function saveOrderAsShipped(\M2E\AmazonMcf\Model\Order $order): void
    {
        $order->setStatusShipped();
        $order->setShippingDate(\M2E\Core\Helper\Date::createCurrentGmt());
        $this->orderRepository->save($order);
    }

    private function skipOrder(string $warningMessage, \M2E\AmazonMcf\Model\Order $order): void
    {
        $this->orderLogger->warning($warningMessage, $order->getId());

        $order->setStatusSkipped();
        $this->orderRepository->save($order);
    }
}
