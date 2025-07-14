<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Magento\Order\ShipOrderService;

class TrackCreationFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(): \Magento\Sales\Api\Data\ShipmentTrackCreationInterface
    {
        return $this->objectManager->create(
            \Magento\Sales\Api\Data\ShipmentTrackCreationInterface::class
        );
    }
}
