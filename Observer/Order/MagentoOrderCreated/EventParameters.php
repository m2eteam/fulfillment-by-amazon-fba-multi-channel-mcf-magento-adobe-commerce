<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Observer\Order\MagentoOrderCreated;

class EventParameters
{
    private const REGION_AMERICA = 'america';
    private const REGION_EUROPE = 'europe';
    private const REGION_ASIA_PACIFIC = 'asia-pacific';
    private const REGION_MAP = [
        self::REGION_AMERICA => \M2E\AmazonMcf\Model\Account::REGION_AMERICA,
        self::REGION_EUROPE => \M2E\AmazonMcf\Model\Account::REGION_EUROPE,
        self::REGION_ASIA_PACIFIC => \M2E\AmazonMcf\Model\Account::REGION_ASIA_PACIFIC,
    ];

    /** Region flags are necessary to maintain backward compatibility. Give priority to regions */
    private const REGION_FLAG_IS_AMERICA = 'is_american_region';
    private const REGION_FLAG_IS_EUROPE = 'is_european_region';
    private const REGION_FLAG_IS_ASIA_PACIFIC = 'is_asian_pacific_region';
    private const REGION_FLAG_MAP = [
        self::REGION_FLAG_IS_AMERICA => \M2E\AmazonMcf\Model\Account::REGION_AMERICA,
        self::REGION_FLAG_IS_EUROPE => \M2E\AmazonMcf\Model\Account::REGION_EUROPE,
        self::REGION_FLAG_IS_ASIA_PACIFIC => \M2E\AmazonMcf\Model\Account::REGION_ASIA_PACIFIC,
    ];

    public string $channel;
    public int $channelOrderId;
    public int $magentoOrderId;
    public string $magentoOrderIncrementId;
    public string $region;
    public ?string $channelExternalOrderId;
    public ?\DateTime $channelPurchaseDate;

    private static array $resultOfResolveRegion = [
        'is_resolved' => false,
        'value' => null,
    ];

    public function __construct(\Magento\Framework\Event $event)
    {
        if (!self::isValid($event)) {
            throw new \RuntimeException('Event parameters is not valid');
        }

        $this->channel = $event->getData('channel');
        $this->channelOrderId = $event->getData('channel_order_id');
        $this->magentoOrderId = $event->getData('magento_order_id');
        $this->magentoOrderIncrementId = $event->getData('magento_order_increment_id');
        $this->region = self::resolveRegion($event);
        $this->channelExternalOrderId = $event->getData('channel_external_order_id');
        $this->channelPurchaseDate = $event->getData('channel_purchase_date');
    }

    public static function isValid(\Magento\Framework\Event $event): bool
    {
        return $event->getData('channel') !== null
            && $event->getData('channel_order_id') !== null
            && $event->getData('magento_order_id') !== null
            && $event->getData('magento_order_increment_id') !== null
            && self::resolveRegion($event) !== null;
    }

    private static function resolveRegion(\Magento\Framework\Event $event): ?string
    {
        if (self::$resultOfResolveRegion['is_resolved']) {
            return self::$resultOfResolveRegion['value'];
        }

        $value = self::REGION_MAP[$event->getData('region')] ?? null;

        if ($value === null && $event->getData(self::REGION_FLAG_IS_AMERICA)) {
            $value = self::REGION_FLAG_MAP[self::REGION_FLAG_IS_AMERICA];
        }

        if ($value === null && $event->getData(self::REGION_FLAG_IS_EUROPE)) {
            $value = self::REGION_FLAG_MAP[self::REGION_FLAG_IS_EUROPE];
        }

        if ($value === null && $event->getData(self::REGION_FLAG_IS_ASIA_PACIFIC)) {
            $value = self::REGION_FLAG_MAP[self::REGION_FLAG_IS_ASIA_PACIFIC];
        }

        self::$resultOfResolveRegion['is_resolved'] = true;

        return self::$resultOfResolveRegion['value'] = $value;
    }
}
