<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Order;

class QtyReservationManager
{
    private Repository $orderRepository;
    private \M2E\AmazonMcf\Model\Product\Repository $productRepository;
    private \M2E\AmazonMcf\Model\Product\Log\Logger $productLogger;

    public function __construct(
        Repository $orderRepository,
        \M2E\AmazonMcf\Model\Product\Repository $productRepository,
        \M2E\AmazonMcf\Model\Product\Log\Logger $productLogger
    ) {
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
        $this->productLogger = $productLogger;
    }

    public function reserveQty(\M2E\AmazonMcf\Model\Order $order): void
    {
        if ($order->isExistsQtyReservationDate()) {
            return;
        }

        foreach ($order->getItems() as $orderItem) {
            $product = $this->productRepository->find($orderItem->getProductId());
            if ($product === null) {
                throw new \LogicException(sprintf('Product not found by id - "%d".', $orderItem->getProductId()));
            }

            $oldQty = $product->getQty();
            $product->decrementQty($orderItem->getQty());
            $this->productRepository->save($product);

            $this->addNoticeLogForReserve($oldQty, $product, $order);
        }

        $order->setQtyReservationDate(\M2E\Core\Helper\Date::createCurrentGmt());
        $this->orderRepository->save($order);
    }

    private function addNoticeLogForReserve(
        int $oldQty,
        \M2E\AmazonMcf\Model\Product $product,
        \M2E\AmazonMcf\Model\Order $order
    ): void {
        $this->productLogger->notice(
            (string)__(
                'Product QTY changed from %oldQty to %newQty. The quantity was reserved for Order ID "%incrementId"',
                [
                    'oldQty' => $oldQty,
                    'newQty' => $product->getQty(),
                    'incrementId' => $order->getMagentoOrderIncrementId(),
                ]
            ),
            $product->getId(),
            \M2E\AmazonMcf\Model\Logger\Initiator::INITIATOR_EXTENSION
        );
    }

    public function releaseQty(\M2E\AmazonMcf\Model\Order $order): void
    {
        if (!$order->isExistsQtyReservationDate()) {
            return;
        }

        foreach ($order->getItems() as $orderItem) {
            $product = $this->productRepository->find($orderItem->getProductId());
            if (
                $product === null
                || $order->getQtyReservationDate() < $product->getQtyLastUpdateDate()
            ) {
                continue;
            }

            $oldQty = $product->getQty();
            $product->incrementQty($orderItem->getQty());
            $this->productRepository->save($product);

            $this->addNoticeLogForRelease($oldQty, $product, $order);
        }

        $order->clearQtyReservationDate();
        $this->orderRepository->save($order);
    }

    private function addNoticeLogForRelease(
        int $oldQty,
        \M2E\AmazonMcf\Model\Product $product,
        \M2E\AmazonMcf\Model\Order $order
    ): void {
        $this->productLogger->notice(
            (string)__(
                'Product QTY changed from %oldQty to %newQty. The quantity was released for Order ID "%incrementId".',
                [
                    'oldQty' => $oldQty,
                    'newQty' => $product->getQty(),
                    'incrementId' => $order->getMagentoOrderIncrementId(),
                ]
            ),
            $product->getId(),
            \M2E\AmazonMcf\Model\Logger\Initiator::INITIATOR_EXTENSION
        );
    }
}
