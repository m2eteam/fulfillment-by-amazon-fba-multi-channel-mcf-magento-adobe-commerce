<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model;

class ChannelConfig
{
    private const GROUP_PREFIX = '/channel';

    private const GROUP_CHANNEL_EBAY = self::GROUP_PREFIX . '/ebay/';
    private const GROUP_CHANNEL_WALMART = self::GROUP_PREFIX . '/walmart/';
    private const GROUP_CHANNEL_MAGENTO = self::GROUP_PREFIX . '/magento/';
    private const GROUP_CHANNEL_TIKTOK_SHOP = self::GROUP_PREFIX . '/tts/';
    private const GROUP_CHANNEL_KAUFLAND = self::GROUP_PREFIX . '/kaufland/';
    private const GROUP_CHANNEL_ONBUY = self::GROUP_PREFIX . '/onbuy/';
    private const GROUP_CHANNEL_OTTO = self::GROUP_PREFIX . '/otto/';
    private const GROUP_CHANNEL_TEMU = self::GROUP_PREFIX . '/temu/';

    private const KEY_IS_ENABLED = 'is_enabled';

    private Config\Manager $configManager;

    public function __construct(Config\Manager $configManager)
    {
        $this->configManager = $configManager;
    }

    // ---------------------------------------

    public function isEnabledEbay(): bool
    {
        return (bool)$this->configManager->getGroupValue(self::GROUP_CHANNEL_EBAY, self::KEY_IS_ENABLED);
    }

    public function enableEbay(): void
    {
        $this->configManager->setGroupValue(self::GROUP_CHANNEL_EBAY, self::KEY_IS_ENABLED, 1);
    }

    public function disableEbay(): void
    {
        $this->configManager->setGroupValue(self::GROUP_CHANNEL_EBAY, self::KEY_IS_ENABLED, 0);
    }

    // ---------------------------------------

    public function isEnabledWalmart(): bool
    {
        return (bool)$this->configManager->getGroupValue(self::GROUP_CHANNEL_WALMART, self::KEY_IS_ENABLED);
    }

    public function enableWalmart(): void
    {
        $this->configManager->setGroupValue(self::GROUP_CHANNEL_WALMART, self::KEY_IS_ENABLED, 1);
    }

    public function disableWalmart(): void
    {
        $this->configManager->setGroupValue(self::GROUP_CHANNEL_WALMART, self::KEY_IS_ENABLED, 0);
    }

    // ---------------------------------------

    public function isEnabledMagento(): bool
    {
        return (bool)$this->configManager->getGroupValue(self::GROUP_CHANNEL_MAGENTO, self::KEY_IS_ENABLED);
    }

    public function enableMagento(): void
    {
        $this->configManager->setGroupValue(self::GROUP_CHANNEL_MAGENTO, self::KEY_IS_ENABLED, 1);
    }

    public function disableMagento(): void
    {
        $this->configManager->setGroupValue(self::GROUP_CHANNEL_MAGENTO, self::KEY_IS_ENABLED, 0);
    }

    // ---------------------------------------

    public function isEnabledTikTokShop(): bool
    {
        return (bool)$this->configManager->getGroupValue(self::GROUP_CHANNEL_TIKTOK_SHOP, self::KEY_IS_ENABLED);
    }

    public function enableTikTokShop(): void
    {
        $this->configManager->setGroupValue(self::GROUP_CHANNEL_TIKTOK_SHOP, self::KEY_IS_ENABLED, 1);
    }

    public function disableTikTokShop(): void
    {
        $this->configManager->setGroupValue(self::GROUP_CHANNEL_TIKTOK_SHOP, self::KEY_IS_ENABLED, 0);
    }

    // ---------------------------------------

    public function isEnabledKaufland(): bool
    {
        return (bool)$this->configManager->getGroupValue(self::GROUP_CHANNEL_KAUFLAND, self::KEY_IS_ENABLED);
    }

    public function enableKaufland(): void
    {
        $this->configManager->setGroupValue(self::GROUP_CHANNEL_KAUFLAND, self::KEY_IS_ENABLED, 1);
    }

    public function disableKaufland(): void
    {
        $this->configManager->setGroupValue(self::GROUP_CHANNEL_KAUFLAND, self::KEY_IS_ENABLED, 0);
    }

    // ---------------------------------------

    public function isEnabledOnBuy(): bool
    {
        return (bool)$this->configManager->getGroupValue(self::GROUP_CHANNEL_ONBUY, self::KEY_IS_ENABLED);
    }

    public function enableOnbuy(): void
    {
        $this->configManager->setGroupValue(self::GROUP_CHANNEL_ONBUY, self::KEY_IS_ENABLED, 1);
    }

    public function disableOnbuy(): void
    {
        $this->configManager->setGroupValue(self::GROUP_CHANNEL_ONBUY, self::KEY_IS_ENABLED, 0);
    }

    // ---------------------------------------

    public function isEnabledOtto(): bool
    {
        return (bool)$this->configManager->getGroupValue(self::GROUP_CHANNEL_OTTO, self::KEY_IS_ENABLED);
    }

    public function enableOtto(): void
    {
        $this->configManager->setGroupValue(self::GROUP_CHANNEL_OTTO, self::KEY_IS_ENABLED, 1);
    }

    public function disableOtto(): void
    {
        $this->configManager->setGroupValue(self::GROUP_CHANNEL_OTTO, self::KEY_IS_ENABLED, 0);
    }

    // ---------------------------------------

    public function isEnabledTemu(): bool
    {
        return (bool)$this->configManager->getGroupValue(self::GROUP_CHANNEL_TEMU, self::KEY_IS_ENABLED);
    }

    public function enableTemu(): void
    {
        $this->configManager->setGroupValue(self::GROUP_CHANNEL_TEMU, self::KEY_IS_ENABLED, 1);
    }

    public function disableTemu(): void
    {
        $this->configManager->setGroupValue(self::GROUP_CHANNEL_TEMU, self::KEY_IS_ENABLED, 0);
    }
}
