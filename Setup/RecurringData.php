<?php

namespace M2E\AmazonMcf\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class RecurringData implements InstallDataInterface
{
    private \M2E\AmazonMcf\Helper\Module\Maintenance $maintenance;
    private InstallHandlerCollection $installHandlerCollection;
    private InstallTablesListResolver $installTablesListResolver;
    private UpgradeCollection $upgradeCollection;
    private \M2E\Core\Model\Setup\InstallChecker $installChecker;
    private \M2E\Core\Model\Setup\InstallerFactory $installerFactory;
    private \M2E\Core\Model\Setup\UpgraderFactory $upgraderFactory;
    private MigrateToCore $migrateToCore;

    public function __construct(
        \M2E\AmazonMcf\Helper\Module\Maintenance $maintenance,
        \M2E\AmazonMcf\Setup\InstallHandlerCollection $installHandlerCollection,
        \M2E\AmazonMcf\Setup\InstallTablesListResolver $installTablesListResolver,
        \M2E\AmazonMcf\Setup\UpgradeCollection $upgradeCollection,
        \M2E\Core\Model\Setup\InstallChecker $installChecker,
        \M2E\Core\Model\Setup\InstallerFactory $installerFactory,
        \M2E\Core\Model\Setup\UpgraderFactory $upgraderFactory,
        \M2E\AmazonMcf\Setup\MigrateToCore $migrateToCore
    ) {
        $this->maintenance = $maintenance;
        $this->installHandlerCollection = $installHandlerCollection;
        $this->installTablesListResolver = $installTablesListResolver;
        $this->upgradeCollection = $upgradeCollection;
        $this->installChecker = $installChecker;
        $this->installerFactory = $installerFactory;
        $this->upgraderFactory = $upgraderFactory;
        $this->migrateToCore = $migrateToCore;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context): void
    {
        if ($this->migrateToCore->isNeedMigrate()) {
            $this->migrateToCore->migrate($setup->getConnection());
        }

        if (!$this->installChecker->isInstalled(\M2E\AmazonMcf\Helper\Module::IDENTIFIER)) {
            $installer = $this->installerFactory->create(
                \M2E\AmazonMcf\Helper\Module::IDENTIFIER,
                $this->installHandlerCollection,
                $this->installTablesListResolver,
                $setup,
                $this->maintenance,
            );
            $installer->install();

            return;
        }

        $upgrader = $this->upgraderFactory->create(
            \M2E\AmazonMcf\Helper\Module::IDENTIFIER,
            $this->upgradeCollection,
            $setup,
            $this->maintenance,
        );
        $upgrader->upgrade();
    }
}
