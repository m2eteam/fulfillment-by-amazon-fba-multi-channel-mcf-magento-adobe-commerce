<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Order;

class ChannelChecker
{
    private \M2E\AmazonMcf\Model\ChannelConfig $channelConfig;
    private \M2E\AmazonMcf\Helper\TikTokShop $tikTokShopHelper;
    private \M2E\AmazonMcf\Helper\Kaufland $kauflandHelper;
    private \M2E\AmazonMcf\Helper\OnBuy $onBuyHelper;
    private \M2E\AmazonMcf\Helper\Otto $ottoHelper;
    private \M2E\AmazonMcf\Helper\Temu $temuHelper;

    private array $runtimeCache = [];

    public function __construct(
        \M2E\AmazonMcf\Model\ChannelConfig $channelConfig,
        \M2E\AmazonMcf\Helper\TikTokShop $tikTokShopHelper,
        \M2E\AmazonMcf\Helper\Kaufland $kauflandHelper,
        \M2E\AmazonMcf\Helper\OnBuy $onBuyHelper,
        \M2E\AmazonMcf\Helper\Otto $ottoHelper,
        \M2E\AmazonMcf\Helper\Temu $temuHelper
    ) {
        $this->channelConfig = $channelConfig;
        $this->tikTokShopHelper = $tikTokShopHelper;
        $this->kauflandHelper = $kauflandHelper;
        $this->onBuyHelper = $onBuyHelper;
        $this->ottoHelper = $ottoHelper;
        $this->temuHelper = $temuHelper;
    }

    public function isAllowedChannel(string $channel): bool
    {
        return $this->runtimeCache[$channel]
            ?? $this->runtimeCache[$channel] = $this->isAllowed($channel);
    }

    public function isDisallowedChannel(string $channel): bool
    {
        return !$this->isAllowedChannel($channel);
    }

    private function isAllowed(string $channel): bool
    {
        if ($channel === \M2E\AmazonMcf\Model\Order::CHANNEL_EBAY) {
            return $this->channelConfig->isEnabledEbay();
        }

        if ($channel === \M2E\AmazonMcf\Model\Order::CHANNEL_WALMART) {
            return $this->channelConfig->isEnabledWalmart();
        }

        if ($channel === \M2E\AmazonMcf\Model\Order::CHANNEL_MAGENTO) {
            return $this->channelConfig->isEnabledMagento();
        }

        if ($channel === \M2E\AmazonMcf\Model\Order::CHANNEL_TIKTOK_SHOP) {
            return $this->tikTokShopHelper->isModuleEnabled()
                && $this->channelConfig->isEnabledTikTokShop();
        }

        if ($channel === \M2E\AmazonMcf\Model\Order::CHANNEL_KAUFLAND) {
            return $this->kauflandHelper->isModuleEnabled()
                && $this->channelConfig->isEnabledKaufland();
        }

        if ($channel === \M2E\AmazonMcf\Model\Order::CHANNEL_ONBUY) {
            return $this->onBuyHelper->isModuleEnabled()
                && $this->channelConfig->isEnabledOnBuy();
        }

        if ($channel === \M2E\AmazonMcf\Model\Order::CHANNEL_OTTO) {
            return $this->ottoHelper->isModuleEnabled()
                && $this->channelConfig->isEnabledOtto();
        }

        if ($channel === \M2E\AmazonMcf\Model\Order::CHANNEL_TEMU) {
            return $this->temuHelper->isModuleEnabled()
                && $this->channelConfig->isEnabledTemu();
        }

        return false;
    }
}
