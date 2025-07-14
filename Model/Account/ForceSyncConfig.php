<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Account;

class ForceSyncConfig
{
    private const GROUP = '/account/force_sync/';
    private const KEY_IS_ENABLED = 'is_enabled';

    private \M2E\AmazonMcf\Model\Config\Manager $configManager;

    public function __construct(\M2E\AmazonMcf\Model\Config\Manager $configManager)
    {
        $this->configManager = $configManager;
    }

    public function isEnabled(): bool
    {
        return (bool)$this->configManager->getGroupValue(
            self::GROUP,
            self::KEY_IS_ENABLED
        );
    }

    public function isDisabled(): bool
    {
        return !$this->isEnabled();
    }

    public function enable(): void
    {
        $this->configManager->setGroupValue(
            self::GROUP,
            self::KEY_IS_ENABLED,
            1
        );
    }

    public function disable(): void
    {
        $this->configManager->setGroupValue(
            self::GROUP,
            self::KEY_IS_ENABLED,
            0
        );
    }
}
