<?php

namespace M2E\AmazonMcf\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class Uninstall implements \Magento\Framework\Setup\UninstallInterface
{
    private \M2E\Core\Model\Setup\UninstallFactory $uninstallFactory;
    private InstallTablesListResolver $installTablesListResolver;
    private \M2E\AmazonMcf\Model\Config\Manager $configManager;
    private \M2E\AmazonMcf\Model\VariablesDir $variablesDir;
    private MagentoCoreConfigSettings $magentoCoreConfigSettings;

    public function __construct(
        \M2E\Core\Model\Setup\UninstallFactory $uninstallFactory,
        \M2E\AmazonMcf\Setup\InstallTablesListResolver $installTablesListResolver,
        \M2E\AmazonMcf\Model\Config\Manager $configManager,
        \M2E\AmazonMcf\Model\VariablesDir $variablesDir,
        \M2E\AmazonMcf\Setup\MagentoCoreConfigSettings $magentoCoreConfigSettings
    ) {
        $this->uninstallFactory = $uninstallFactory;
        $this->installTablesListResolver = $installTablesListResolver;
        $this->configManager = $configManager;
        $this->variablesDir = $variablesDir;
        $this->magentoCoreConfigSettings = $magentoCoreConfigSettings;
    }

    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context): void
    {
        $this->uninstallFactory
            ->create(
                \M2E\AmazonMcf\Helper\Module::IDENTIFIER,
                $this->installTablesListResolver,
                $this->configManager->getAdapter(),
                $this->variablesDir->getAdapter(),
                $this->magentoCoreConfigSettings,
                $setup,
            )
            ->process();
    }
}
