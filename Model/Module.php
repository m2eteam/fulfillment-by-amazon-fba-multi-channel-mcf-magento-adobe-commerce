<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model;

class Module implements \M2E\Core\Model\ModuleInterface
{
    private bool $areImportantTablesExist;

    private \M2E\Core\Model\Module\Adapter $adapter;
    private \M2E\Core\Model\Module\AdapterFactory $adapterFactory;
    private \M2E\AmazonMcf\Model\Config\Manager $configManager;
    private \M2E\AmazonMcf\Model\Registry\Manager $registryManager;
    private \M2E\Core\Helper\Module\Database\Structure $moduleDatabaseHelper;
    private \Magento\Framework\App\ResourceConnection $resourceConnection;

    public function __construct(
        \M2E\Core\Model\Module\AdapterFactory $adapterFactory,
        Registry\Manager $registryManager,
        Config\Manager $configManager,
        \M2E\Core\Helper\Module\Database\Structure $moduleDatabaseHelper,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        $this->registryManager = $registryManager;
        $this->adapterFactory = $adapterFactory;
        $this->configManager = $configManager;
        $this->moduleDatabaseHelper = $moduleDatabaseHelper;
        $this->resourceConnection = $resourceConnection;
    }

    public function getName(): string
    {
        return 'AmazonMcf-m2';
    }

    public function getPublicVersion(): string
    {
        return $this->getAdapter()->getPublicVersion();
    }

    public function getSetupVersion(): string
    {
        return $this->getAdapter()->getSetupVersion();
    }

    public function getSchemaVersion(): string
    {
        return $this->getAdapter()->getSchemaVersion();
    }

    public function getDataVersion(): string
    {
        return $this->getAdapter()->getDataVersion();
    }

    public function hasLatestVersion(): bool
    {
        return $this->getAdapter()->hasLatestVersion();
    }

    public function setLatestVersion(string $version): void
    {
        $this->getAdapter()->setLatestVersion($version);
    }

    public function getLatestVersion(): ?string
    {
        return $this->getAdapter()->getLatestVersion();
    }

    public function isDisabled(): bool
    {
        return $this->getAdapter()->isDisabled();
    }

    public function disable(): void
    {
        $this->getAdapter()->disable();
    }

    public function enable(): void
    {
        $this->getAdapter()->enable();
    }

    public function isReadyToWork(): bool
    {
        return $this->areImportantTablesExist();
    }

    public function areImportantTablesExist(): bool
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (isset($this->areImportantTablesExist)) {
            return $this->areImportantTablesExist;
        }

        $importantTables = [
            \M2E\AmazonMcf\Helper\Module\Database\Tables::TABLE_NAME_ACCOUNT,
        ];

        $result = true;
        foreach ($importantTables as $table) {
            $tableName = $this->moduleDatabaseHelper->getTableNameWithPrefix($table);
            if (!$this->resourceConnection->getConnection()->isTableExists($tableName)) {
                $result = false;
                break;
            }
        }

        return $this->areImportantTablesExist = $result;
    }

    // ---------------------------------------

    public function getAdapter(): \M2E\Core\Model\Module\Adapter
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (!isset($this->adapter)) {
            $this->adapter = $this->adapterFactory->create(
                \M2E\AmazonMcf\Helper\Module::IDENTIFIER,
                $this->registryManager->getAdapter(),
                $this->configManager->getAdapter()
            );
        }

        return $this->adapter;
    }
}
