<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Order\StatusProcessor;

class ShippedStatusProcessor
{
    private \M2E\AmazonMcf\Model\Magento\Order\ShipOrderService $shipMagentoOrderService;
    private \M2E\AmazonMcf\Model\Order\Log\Logger $orderLogger;
    private \M2E\AmazonMcf\Model\Order\Repository $orderRepository;

    public function __construct(
        \M2E\AmazonMcf\Model\Magento\Order\ShipOrderService $shipMagentoOrderService,
        \M2E\AmazonMcf\Model\Order\Repository $orderRepository,
        \M2E\AmazonMcf\Model\Order\Log\Logger $orderLogger
    ) {
        $this->shipMagentoOrderService = $shipMagentoOrderService;
        $this->orderLogger = $orderLogger;
        $this->orderRepository = $orderRepository;
    }

    public function process(\M2E\AmazonMcf\Model\Order $order): void
    {
        if (!$order->isStatusShipped()) {
            return;
        }

        $magentoOrder = $order->getMagentoOrder();
        if (!$this->shipMagentoOrderService->canProcess($magentoOrder)) {
            $this->skipOrder((string)__('Magento order cannot be shipped.'), $order);

            return;
        }

        $shipmentItems = $this->retrieveShipmentItems($order);
        $result = $this->shipMagentoOrderService->process($magentoOrder, $shipmentItems);
        foreach ($result->getMessages() as $message) {
            $this->orderLogger->error($message, $order->getId());
        }

        $magentoShipments = $result->getCreatedShipments();
        if (empty($magentoShipments)) {
            $this->skipOrder((string)__('Order was not updated to "Shipped".'), $order);

            return;
        }

        foreach ($magentoShipments as $createdShipment) {
            $this->orderLogger->notice(
                (string)__(
                    'Magento Shipment "%shipment_id" has been created.',
                    ['shipment_id' => $createdShipment->getIncrementId()]
                ),
                $order->getId()
            );
        }

        $order->setStatusComplete();
        $this->orderRepository->save($order);
    }

    /**
     * @return \M2E\AmazonMcf\Model\Magento\Order\ShipOrderService\ShipmentItem[]
     */
    private function retrieveShipmentItems(\M2E\AmazonMcf\Model\Order $order): array
    {
        $shipmentItems = [];
        foreach ($order->getItems() as $orderItem) {
            $shipmentItems[] = new \M2E\AmazonMcf\Model\Magento\Order\ShipOrderService\ShipmentItem(
                $orderItem->getMagentoOrderItemId(),
                $orderItem->getQty(),
                $orderItem->getTrackingNumber(),
                $orderItem->isExistsCarrierCode() ? $orderItem->getCarrierCode() : null
            );
        }

        return $shipmentItems;
    }

    private function skipOrder(string $warningMessage, \M2E\AmazonMcf\Model\Order $order): void
    {
        $this->orderLogger->warning($warningMessage, $order->getId());

        $order->setStatusSkipped();
        $this->orderRepository->save($order);
    }
}
