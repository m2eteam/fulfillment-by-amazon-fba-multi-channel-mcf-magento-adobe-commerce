<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Helper\Module;

class Maintenance implements \M2E\Core\Model\Module\MaintenanceInterface
{
    private const MAINTENANCE_CONFIG_PATH = \M2E\AmazonMcf\Helper\Module::MAGENTO_CONFIG_KEY_PREFIX . 'maintenance';

    private \M2E\Core\Model\Module\Maintenance\AdapterFactory $adapterFactory;
    private \M2E\Core\Model\Module\Maintenance\Adapter $adapter;

    public function __construct(
        \M2E\Core\Model\Module\Maintenance\AdapterFactory $adapterFactory
    ) {
        $this->adapterFactory = $adapterFactory;
    }

    public function isEnabled(): bool
    {
        return $this->getAdapter()->isEnabled();
    }

    public function enable(): void
    {
        $this->getAdapter()->enable();
    }

    public function disable(): void
    {
        $this->getAdapter()->disable();
    }

    public function isEnabledDueLowMagentoVersion(): bool
    {
        return false;
    }

    public function enableDueLowMagentoVersion(): void
    {
    }

    public function getAdapter(): \M2E\Core\Model\Module\Maintenance\Adapter
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (!isset($this->adapter)) {
            $this->adapter = $this->adapterFactory->create(
                self::MAINTENANCE_CONFIG_PATH
            );
        }

        return $this->adapter;
    }
}
