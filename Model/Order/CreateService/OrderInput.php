<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Order\CreateService;

class OrderInput
{
    public string $channel;
    public int $channelOrderId;
    public int $magentoOrderId;
    public string $magentoOrderIncrementId;
    public string $region;
    public ?string $channelExternalOrderId;
    public ?\DateTime $channelPurchaseDate;

    public function __construct(
        string $channel,
        int $channelOrderId,
        int $magentoOrderId,
        string $magentoOrderIncrementId,
        string $region,
        ?string $channelExternalOrderId,
        ?\DateTime $channelPurchaseDate
    ) {
        $this->channel = $channel;
        $this->channelOrderId = $channelOrderId;
        $this->magentoOrderId = $magentoOrderId;
        $this->magentoOrderIncrementId = $magentoOrderIncrementId;
        $this->region = $region;
        $this->channelExternalOrderId = $channelExternalOrderId;
        $this->channelPurchaseDate = $channelPurchaseDate;
    }
}
