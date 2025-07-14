<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Order\StatusProcessor;

class PendingStatusProcessor
{
    private \M2E\AmazonMcf\Model\Amazon\CreateOrder $amazonCreateOrder;
    private \M2E\AmazonMcf\Model\Product\Repository $productRepository;
    private \M2E\AmazonMcf\Model\Order\QtyReservationManager $qtyReservationManager;
    private \M2E\AmazonMcf\Model\Order\Log\Logger $orderLogger;
    private PendingStatusProcessor\ShippingItemsCalculator $shippingItemsCalculator;
    private \M2E\AmazonMcf\Model\Order\Repository $orderRepository;
    private \M2E\AmazonMcf\Model\Order\Item\Repository $orderItemRepository;
    private \M2E\AmazonMcf\Model\Order\ItemFactory $orderItemFactory;

    public function __construct(
        \M2E\AmazonMcf\Model\Amazon\CreateOrder $amazonCreateOrder,
        \M2E\AmazonMcf\Model\Product\Repository $productRepository,
        \M2E\AmazonMcf\Model\Order\QtyReservationManager $qtyReservationManager,
        PendingStatusProcessor\ShippingItemsCalculator $shippingItemsCalculator,
        \M2E\AmazonMcf\Model\Order\Repository $orderRepository,
        \M2E\AmazonMcf\Model\Order\Item\Repository $orderItemRepository,
        \M2E\AmazonMcf\Model\Order\ItemFactory $orderItemFactory,
        \M2E\AmazonMcf\Model\Order\Log\Logger $orderLogger
    ) {
        $this->amazonCreateOrder = $amazonCreateOrder;
        $this->productRepository = $productRepository;
        $this->qtyReservationManager = $qtyReservationManager;
        $this->shippingItemsCalculator = $shippingItemsCalculator;
        $this->orderItemRepository = $orderItemRepository;
        $this->orderRepository = $orderRepository;
        $this->orderItemFactory = $orderItemFactory;
        $this->orderLogger = $orderLogger;
    }

    /**
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\AuthorizationException
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\SystemUnavailableException
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\ThrottlingException
     */
    public function process(\M2E\AmazonMcf\Model\Order $order): void
    {
        if (!$order->isPendingStatus()) {
            return;
        }

        if (
            !$order->isExistsAccountId()
            && !$this->assignAccount($order)
        ) {
            return;
        }

        $resultOfShippingItemsCalculation = $this->shippingItemsCalculator->getCalculatedItems($order);
        if ($resultOfShippingItemsCalculation->isExistsMessage()) {
            $this->skipOrder(
                (string)__(
                    'Failed to create MCF order: %reason',
                    ['reason' => $resultOfShippingItemsCalculation->getMessage()]
                ),
                $order
            );

            return;
        }

        $magentoShippingAddress = $order->getMagentoOrder()->getShippingAddress();
        if ($magentoShippingAddress === null) {
            $this->skipOrder(
                (string)__('Failed to create MCF order: Shipping address is missing in Magento order.'),
                $order
            );

            return;
        }

        $fulfillmentOrderCreated = $this->createFulfillmentOrder(
            $order,
            $magentoShippingAddress,
            $resultOfShippingItemsCalculation->getItems()
        );
        if ($fulfillmentOrderCreated) {
            $this->qtyReservationManager->reserveQty($order);
        }
    }

    private function assignAccount(\M2E\AmazonMcf\Model\Order $order): bool
    {
        $account = $this->findAccount($order);
        if ($account === null) {
            $this->skipOrder(
                (string)__('Failed to create MCF order: No associated Amazon channel account found'),
                $order
            );

            return false;
        }

        if ($account->getRegion() !== $order->getRegion()) {
            $this->skipOrder(
                (string)__(
                    'Failed to create MCF order: Region specified in the order does not match Amazon account region'
                ),
                $order
            );

            return false;
        }

        $order->setAccountId($account->getId());
        $this->orderRepository->save($order);

        return true;
    }

    private function findAccount(\M2E\AmazonMcf\Model\Order $order): ?\M2E\AmazonMcf\Model\Account
    {
        $magentoProductsIds = [];
        foreach ($order->getMagentoOrder()->getAllItems() as $orderItem) {
            $magentoProductsIds[] = $orderItem->getProductId();
        }

        $products = $this->productRepository->retrieveByMagentoProductsIds($magentoProductsIds);
        foreach ($products as $product) {
            if ($product->getAccount()->getRegion() === $order->getRegion()) {
                return $product->getAccount();
            }
        }

        return null;
    }

    /**
     * @param PendingStatusProcessor\ShippingItemsCalculator\Item[] $shippingItems
     *
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\AuthorizationException
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\SystemUnavailableException
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\ThrottlingException
     */
    private function createFulfillmentOrder(
        \M2E\AmazonMcf\Model\Order $order,
        \Magento\Sales\Model\Order\Address $magentoShippingAddress,
        array $shippingItems
    ): bool {
        $resultOfProcess = $this->amazonCreateOrder->process(
            $order,
            $magentoShippingAddress,
            $shippingItems
        );

        if (!empty($resultOfProcess->getMessages())) {
            $texts = array_map(function (\M2E\AmazonMcf\Model\Amazon\Connector\Message\Message $message) {
                return $message->getText();
            }, $resultOfProcess->getMessages());
            $this->skipOrder(implode(' ', $texts), $order);

            return false;
        }

        $this->saveOrderWithStatusWaitCreatedPackage(
            $resultOfProcess->getSellerFulfillmentId(),
            $shippingItems,
            $order
        );

        return true;
    }

    private function skipOrder(string $warningMessage, \M2E\AmazonMcf\Model\Order $order): void
    {
        $this->orderLogger->warning($warningMessage, $order->getId());

        $order->setStatusSkipped();
        $this->orderRepository->save($order);
    }

    /**
     * @param PendingStatusProcessor\ShippingItemsCalculator\Item[] $shippingItems
     */
    public function saveOrderWithStatusWaitCreatedPackage(
        string $sellerFulfillmentId,
        array $shippingItems,
        \M2E\AmazonMcf\Model\Order $order
    ): void {
        foreach ($shippingItems as $shippingItem) {
            $orderItem = $this->orderItemFactory
                ->create()
                ->init(
                    $order->getId(),
                    $shippingItem->getProduct()->getId(),
                    (int)$shippingItem->getMagentoOrderItem()->getItemId(),
                    $shippingItem->getSellerFulfillmentItemId(),
                    $shippingItem->getChannelSku(),
                    $shippingItem->getQty()
                );

            $this->orderItemRepository->create($orderItem);
        }

        $order->setSellerFulfillmentId($sellerFulfillmentId);
        $order->setStatusWaitCreatedPackage();

        $this->orderRepository->save($order);
    }
}
