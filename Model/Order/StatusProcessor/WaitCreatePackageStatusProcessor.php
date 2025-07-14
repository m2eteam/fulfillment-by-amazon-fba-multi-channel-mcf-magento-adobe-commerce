<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Order\StatusProcessor;

class WaitCreatePackageStatusProcessor
{
    private \M2E\AmazonMcf\Model\Amazon\RetrievePackages $retrieveShipmentPackages;
    private \M2E\AmazonMcf\Model\Order\Repository $orderRepository;
    private \M2E\AmazonMcf\Model\Order\Item\Repository $orderItemRepository;
    private \M2E\AmazonMcf\Model\Order\Log\Logger $orderLogger;

    public function __construct(
        \M2E\AmazonMcf\Model\Amazon\RetrievePackages $retrieveShipmentPackages,
        \M2E\AmazonMcf\Model\Order\Repository $orderRepository,
        \M2E\AmazonMcf\Model\Order\Item\Repository $orderItemRepository,
        \M2E\AmazonMcf\Model\Order\Log\Logger $orderLogger
    ) {
        $this->retrieveShipmentPackages = $retrieveShipmentPackages;
        $this->orderItemRepository = $orderItemRepository;
        $this->orderRepository = $orderRepository;
        $this->orderLogger = $orderLogger;
    }

    /**
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\AuthorizationException
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\SystemUnavailableException
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\ThrottlingException
     */
    public function process(\M2E\AmazonMcf\Model\Order $order): void
    {
        if (!$order->isWaitCreatedPackageStatus()) {
            return;
        }

        $packages = $this->retrievePackagesFromAmazon($order);
        if (empty($packages)) {
            return;
        }

        $this->savePackageNumbersToOrderItems($order->getItems(), $packages);
        if (!$this->isAllOrderItemsWithPackageNumbers($order->getItems())) {
            $this->skipOrder(
                (string)__(
                    'Order was not updated: unable to retrieve package number from Amazon for the Items in the order '
                ),
                $order
            );

            return;
        }

        $order->setStatusWaitShip();
        $this->orderRepository->save($order);
    }

    /**
     * @return \M2E\AmazonMcf\Model\Amazon\RetrievePackages\Package[]
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\AuthorizationException
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\SystemUnavailableException
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\ThrottlingException
     */
    private function retrievePackagesFromAmazon(\M2E\AmazonMcf\Model\Order $order): array
    {
        $result = $this->retrieveShipmentPackages->process(
            $order->getSellerFulfillmentId(),
            $order->getAccount()
        );

        if (!empty($result->getMessages())) {
            $messagesTexts = array_map(function (\M2E\AmazonMcf\Model\Amazon\Connector\Message\Message $message) {
                return $message->getText();
            }, $result->getMessages());
            $this->skipOrder(implode(' ', $messagesTexts), $order);

            return [];
        }

        if ($result->isStatusProcessing()) {
            return [];
        }

        $packages = $result->getPackages();
        if (empty($packages)) {
            $this->skipOrder(
                (string)__('Order update failed: Shipping information was not provided by Amazon'),
                $order
            );

            return [];
        }

        if ($result->isStatusInvalid()) {
            $this->skipOrder(
                (string)__('Order is Unprocessed because its status on the Channel is not valid.'),
                $order
            );

            return [];
        }

        if ($result->isStatusCompleted()) {
            return $packages;
        }

        return [];
    }

    /**
     * @param \M2E\AmazonMcf\Model\Order\Item[] $orderItems
     * @param \M2E\AmazonMcf\Model\Amazon\RetrievePackages\Package[] $shipmentPackages
     *
     * @return void
     */
    private function savePackageNumbersToOrderItems(array $orderItems, array $shipmentPackages): void
    {
        /** @var array<string, \M2E\AmazonMcf\Model\Order\Item> $orderItemsByFulfillmentItemId */
        $orderItemsByFulfillmentItemId = [];
        foreach ($orderItems as $orderItem) {
            if ($orderItem->isExistsPackageNumber()) {
                continue;
            }
            $orderItemsByFulfillmentItemId[$orderItem->getSellerFulfillmentItemId()] = $orderItem;
        }

        foreach ($shipmentPackages as $shipmentPackage) {
            foreach ($shipmentPackage->getSellerFulfillmentItemIds() as $fulfilmentItemId) {
                if (!isset($orderItemsByFulfillmentItemId[$fulfilmentItemId])) {
                    continue;
                }
                $orderItem = $orderItemsByFulfillmentItemId[$fulfilmentItemId];
                $orderItem->setPackageNumber($shipmentPackage->getPackageNumber());
                $this->orderItemRepository->save($orderItem);
            }
        }
    }

    /**
     * @param \M2E\AmazonMcf\Model\Order\Item[] $orderItems
     */
    public function isAllOrderItemsWithPackageNumbers(array $orderItems): bool
    {
        if (empty($orderItems)) {
            return false;
        }

        foreach ($orderItems as $orderItem) {
            if (!$orderItem->isExistsPackageNumber()) {
                return false;
            }
        }

        return true;
    }

    private function skipOrder(string $warningMessage, \M2E\AmazonMcf\Model\Order $order): void
    {
        $this->orderLogger->warning($warningMessage, $order->getId());

        $order->setStatusSkipped();
        $this->orderRepository->save($order);
    }
}
