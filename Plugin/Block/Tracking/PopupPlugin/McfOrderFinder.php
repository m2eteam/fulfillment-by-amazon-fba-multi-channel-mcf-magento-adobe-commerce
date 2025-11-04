<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Plugin\Block\Tracking\PopupPlugin;

class McfOrderFinder
{
    private const HASH_ENTITY_TYPE_ORDER_ID = 'order_id';
    private const HASH_ENTITY_TYPE_SHIP_ID = 'ship_id';
    private const HASH_ENTITY_TYPE_TRACK_ID = 'track_id';

    private \Magento\Shipping\Helper\Data $shippingDataHelper;
    private \Magento\Sales\Api\ShipmentRepositoryInterface $shipmentRepository;
    private \Magento\Sales\Api\ShipmentTrackRepositoryInterface $trackRepository;
    private \M2E\AmazonMcf\Model\Order\Repository $mcfOrderRepository;

    public function __construct(
        \Magento\Shipping\Helper\Data $shippingDataHelper,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Api\ShipmentRepositoryInterface $shipmentRepository,
        \Magento\Sales\Api\ShipmentTrackRepositoryInterface $trackRepository,
        \M2E\AmazonMcf\Model\Order\Repository $mcfOrderRepository
    ) {
        $this->shippingDataHelper = $shippingDataHelper;
        $this->shipmentRepository = $shipmentRepository;
        $this->trackRepository = $trackRepository;
        $this->mcfOrderRepository = $mcfOrderRepository;
    }

    public function tryFindByRequest(\Magento\Framework\App\RequestInterface $request): ?\M2E\AmazonMcf\Model\Order
    {
        $hash = $request->getParam('hash');
        if (!$hash) {
            return null;
        }

        $decodedHash = $this->decodeHash($hash);
        if (empty($decodedHash)) {
            return null;
        }

        $magentoOrderId = $this->getMagentoOrderId($decodedHash->entityType, $decodedHash->entityId);
        if (empty($magentoOrderId)) {
            return null;
        }

        $mcfOrder = $this->mcfOrderRepository->findByMagentoOrderId($magentoOrderId);
        if (empty($mcfOrder)) {
            return null;
        }

        return $mcfOrder;
    }

    private function decodeHash(string $hash): ?DecodedHashDTO
    {
        $decodeTrackingHash = $this->shippingDataHelper->decodeTrackingHash($hash);
        if (empty($decodeTrackingHash)) {
            return null;
        }

        $entityType = $decodeTrackingHash['key'] ?? null;
        $entityId = $decodeTrackingHash['id'] ?? null;

        if (
            empty($entityType)
            || empty($entityId)
            || !$this->isValidEntityType((string)$entityType)
        ) {
            return null;
        }

        return new DecodedHashDTO((string)$entityType, (int)$entityId);
    }

    private function getMagentoOrderId(string $entityType, int $id): ?int
    {
        switch ($entityType) {
            case self::HASH_ENTITY_TYPE_ORDER_ID:
                return (int)$id;
            case self::HASH_ENTITY_TYPE_SHIP_ID:
                return (int)$this->shipmentRepository->get($id)->getOrderId();
            case self::HASH_ENTITY_TYPE_TRACK_ID:
                return (int)$this->trackRepository->get($id)->getOrderId();
            default:
                return null;
        }
    }

    private function isValidEntityType(string $entityType): bool
    {
        return in_array($entityType, [
            self::HASH_ENTITY_TYPE_ORDER_ID,
            self::HASH_ENTITY_TYPE_SHIP_ID,
            self::HASH_ENTITY_TYPE_TRACK_ID,
        ], true);
    }
}
