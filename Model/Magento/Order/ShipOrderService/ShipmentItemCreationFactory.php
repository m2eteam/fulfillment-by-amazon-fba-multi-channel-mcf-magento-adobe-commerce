<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Magento\Order\ShipOrderService;

class ShipmentItemCreationFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(): \Magento\Sales\Api\Data\ShipmentItemCreationInterface
    {
        return $this->objectManager->create(
            \Magento\Sales\Api\Data\ShipmentItemCreationInterface::class
        );
    }
}
