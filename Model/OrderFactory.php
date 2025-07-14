<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model;

class OrderFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(
        string $channel,
        int $channelOrderId,
        int $magentoOrderId,
        string $magentoOrderIncrementId,
        string $region,
        ?string $channelExternalOrderId,
        ?\DateTime $channelPurchaseDate
    ): Order {
        $order = $this->createEmpty()
                      ->init(
                          $channel,
                          $magentoOrderId,
                          $magentoOrderIncrementId,
                          $region
                      )
                      ->setChannelOrderId($channelOrderId);

        if ($channelExternalOrderId !== null) {
            $order->setChannelExternalOrderId($channelExternalOrderId);
        }

        if ($channelPurchaseDate !== null) {
            $order->setChannelPurchaseDate($channelPurchaseDate);
        }

        return $order;
    }

    public function createWithMagentoChannel(
        int $magentoOrderId,
        string $magentoOrderIncrementId,
        string $region,
        \DateTime $channelPurchaseDate
    ): Order {
        return $this->createEmpty()
                    ->init(
                        \M2E\AmazonMcf\Model\Order::CHANNEL_MAGENTO,
                        $magentoOrderId,
                        $magentoOrderIncrementId,
                        $region
                    )
                    ->setChannelPurchaseDate($channelPurchaseDate);
    }

    public function createEmpty(): Order
    {
        return $this->objectManager->create(Order::class);
    }
}
