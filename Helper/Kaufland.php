<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Helper;

class Kaufland
{
    private const MODULE_NAME = 'M2E_Kaufland';

    private \Magento\Framework\Module\Manager $moduleManager;

    public function __construct(\Magento\Framework\Module\Manager $moduleManager)
    {
        $this->moduleManager = $moduleManager;
    }

    public function isModuleEnabled(): bool
    {
        return $this->moduleManager->isEnabled(self::MODULE_NAME);
    }

    public function isModuleDisabled(): bool
    {
        return !$this->isModuleEnabled();
    }
}
