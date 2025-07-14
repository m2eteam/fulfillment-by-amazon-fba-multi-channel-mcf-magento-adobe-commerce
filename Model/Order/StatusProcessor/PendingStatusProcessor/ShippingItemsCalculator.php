<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Order\StatusProcessor\PendingStatusProcessor;

class ShippingItemsCalculator
{
    private \M2E\AmazonMcf\Model\Product\Repository $productRepository;

    public function __construct(\M2E\AmazonMcf\Model\Product\Repository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getCalculatedItems(\M2E\AmazonMcf\Model\Order $order): ShippingItemsCalculator\Result
    {
        $result = new ShippingItemsCalculator\Result();

        $magentoOrder = $order->getMagentoOrder();
        if (!$magentoOrder->canShip()) {
            return $result->setMessage((string)__('Magento order cannot be shipped.'));
        }

        $idsOfNotShippedMagentoProducts = $this->findIdsOfNotShippedMagentoProducts($magentoOrder);
        if (empty($idsOfNotShippedMagentoProducts)) {
            return $result->setMessage((string)__('The unshipped products were not found in Magento.'));
        }

        $products = $this->productRepository->findAvailableByMagentoProductId(
            $idsOfNotShippedMagentoProducts,
            $order->getAccountId()
        );

        if (empty($products)) {
            return $result->setMessage(
                (string)__(
                    'Products are not available for Amazon Multi-Channel'
                    . ' Fulfillment: they are either out of stock or not enabled for MCF.'
                )
            );
        }

        if (count($products) !== count($idsOfNotShippedMagentoProducts)) {
            return $result->setMessage(
                (string)__(
                    'The product quantity in Magento order does not match the available MCF product stock.'
                )
            );
        }

        $calculatedItems = $this->getCalculatedResultItems($products, $magentoOrder->getItems());
        if (empty($calculatedItems)) {
            return $result->setMessage(
                (string)__('Products were not found among the available MCF Items.')
            );
        }

        return $result->setItems($calculatedItems);
    }

    /**
     * @return string[]
     */
    private function findIdsOfNotShippedMagentoProducts(\Magento\Sales\Model\Order $magentoOrder): array
    {
        $shipmentItemProductIds = $this->getShipmentItemProductIds($magentoOrder);

        $magentoProductIds = [];
        foreach ($magentoOrder->getItems() as $item) {
            if ($item->getProduct() === null) {
                continue;
            }
            if (!in_array($item->getProductId(), $shipmentItemProductIds)) {
                $magentoProductIds[] = $item->getProductId();
            }
        }

        return $magentoProductIds;
    }

    /**
     * @return int[]
     */
    private function getShipmentItemProductIds(\Magento\Sales\Model\Order $magentoOrder): array
    {
        /** @var \Magento\Sales\Model\Order\Shipment[] $shipments */
        $shipments = $magentoOrder->getShipmentsCollection()->getItems();

        $ids = [];
        foreach ($shipments as $shipment) {
            foreach ($shipment->getItems() as $shipmentItem) {
                $ids[] = $shipmentItem->getProductId();
            }
        }

        return array_unique($ids);
    }

    /**
     * @param \M2E\AmazonMcf\Model\Product[] $products
     * @param \Magento\Sales\Api\Data\OrderItemInterface[] $magentoOrderItems
     *
     * @return ShippingItemsCalculator\Item[]
     */
    private function getCalculatedResultItems(array $products, array $magentoOrderItems): array
    {
        $magentoOrderItemsBySku = [];
        foreach ($magentoOrderItems as $item) {
            $magentoOrderItemsBySku[$item->getSku()] = $item;
        }

        $resultItems = [];
        foreach ($products as $product) {
            $magentoOrderItem = $magentoOrderItemsBySku[$product->getMagentoProductSku()];
            if ($product->getQty() < $magentoOrderItem->getQtyOrdered()) {
                continue;
            }

            $resultItems[] = new ShippingItemsCalculator\Item(
                $magentoOrderItem,
                $product
            );
        }

        return $resultItems;
    }
}
