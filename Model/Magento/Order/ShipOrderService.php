<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Magento\Order;

class ShipOrderService
{
    private const DEFAULT_TRACK_TITLE = 'M2E Amazon MCF';

    private \Magento\Sales\Api\ShipOrderInterface $shipOrderService;
    private ShipOrderService\ShipmentItemCreationFactory $shipmentItemCreationFactory;
    private \Magento\Sales\Api\ShipmentRepositoryInterface $shipmentRepository;
    /** @var \M2E\AmazonMcf\Model\Magento\Order\ShipOrderService\TrackCreationFactory */
    private ShipOrderService\TrackCreationFactory $trackFactory;
    private \M2E\AmazonMcf\Model\SystemLog\Logger $systemLogger;

    public function __construct(
        \Magento\Sales\Api\ShipOrderInterface $shipOrderService,
        \Magento\Sales\Api\ShipmentRepositoryInterface $shipmentRepository,
        ShipOrderService\ShipmentItemCreationFactory $shipmentItemCreationFactory,
        ShipOrderService\TrackCreationFactory $trackFactory,
        \M2E\AmazonMcf\Model\SystemLog\Logger $systemLogger
    ) {
        $this->shipOrderService = $shipOrderService;
        $this->shipmentItemCreationFactory = $shipmentItemCreationFactory;
        $this->shipmentRepository = $shipmentRepository;
        $this->trackFactory = $trackFactory;
        $this->systemLogger = $systemLogger;
    }

    public function canProcess(\Magento\Sales\Model\Order $magentoOrder): bool
    {
        return $magentoOrder->canShip();
    }

    /**
     * @param ShipOrderService\ShipmentItem[] $inputShipmentItems
     */
    public function process(
        \Magento\Sales\Model\Order $magentoOrder,
        array $inputShipmentItems
    ): ShipOrderService\Result {
        $processResult = new ShipOrderService\Result();

        $shipmentItemsGroups = $this->getShipmentItemsGroupedByTrackingNumber($inputShipmentItems);
        foreach ($shipmentItemsGroups as $shipmentItems) {
            $shipmentItemsCreation = $this->createShipmentCreationItems($shipmentItems);
            $track = $this->createTrack(reset($shipmentItems));
            $this->shipOrder(
                $shipmentItemsCreation,
                $magentoOrder,
                $track,
                $processResult
            );
        }

        return $processResult;
    }

    /**
     * @param ShipOrderService\ShipmentItem[] $inputShipmentItems
     *
     * @return array<string, ShipOrderService\ShipmentItem[]>
     */
    private function getShipmentItemsGroupedByTrackingNumber(array $inputShipmentItems): array
    {
        $shipmentItemsByTrackingNumber = [];
        foreach ($inputShipmentItems as $shipmentItem) {
            $shipmentItemsByTrackingNumber[$shipmentItem->getTrackingNumber()][] = $shipmentItem;
        }

        return $shipmentItemsByTrackingNumber;
    }

    /**
     * @param ShipOrderService\ShipmentItem[] $shipmentItems
     *
     * @return \Magento\Sales\Api\Data\ShipmentItemCreationInterface[]
     */
    private function createShipmentCreationItems(array $shipmentItems): array
    {
        $shipmentCreationItems = [];
        foreach ($shipmentItems as $shipmentItem) {
            $shipmentItemCreation = $this->shipmentItemCreationFactory->create();
            $shipmentItemCreation->setOrderItemId($shipmentItem->getMagentoOrderItemId());
            $shipmentItemCreation->setQty($shipmentItem->getQty());

            $shipmentCreationItems[] = $shipmentItemCreation;
        }

        return $shipmentCreationItems;
    }

    private function createTrack(
        ShipOrderService\ShipmentItem $shipmentItem
    ): \Magento\Sales\Api\Data\ShipmentTrackCreationInterface {
        $track = $this->trackFactory->create();
        $track->setTrackNumber($shipmentItem->getTrackingNumber());
        $track->setTitle($shipmentItem->getCarrierCode() ?? self::DEFAULT_TRACK_TITLE);
        $track->setCarrierCode(
            $shipmentItem->getCarrierCode()
            ?? \Magento\Sales\Model\Order\Shipment\Track::CUSTOM_CARRIER_CODE
        );

        return $track;
    }

    /**
     * @param \Magento\Sales\Api\Data\ShipmentItemCreationInterface[] $shipmentItemsCreation
     */
    private function shipOrder(
        array $shipmentItemsCreation,
        \Magento\Sales\Model\Order $magentoOrder,
        \Magento\Sales\Api\Data\ShipmentTrackCreationInterface $track,
        ShipOrderService\Result $processResult
    ): void {
        try {
            $shipmentEntityId = $this->shipOrderService->execute(
                $magentoOrder->getEntityId(),
                $shipmentItemsCreation,
                false,
                false,
                null,
                [$track]
            );
            $shipment = $this->shipmentRepository->get($shipmentEntityId);
            $processResult->addCreatedShipment($shipment);
        } catch (\Magento\Sales\Exception\DocumentValidationException $e) {
            $processResult->addMessage($e->getMessage());

            return;
        } catch (\Throwable $e) {
            $this->systemLogger->exception($e);
            $processResult->addMessage('Failed to create Magento Shipment');
        }
    }
}
