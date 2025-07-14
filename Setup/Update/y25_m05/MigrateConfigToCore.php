<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Setup\Update\y25_m05;

class MigrateConfigToCore extends \M2E\Core\Model\Setup\Upgrade\Entity\AbstractFeature
{
    public function execute(): void
    {
        $connection = $this->getConnection();
        $mcfConfigTable = $this->getFullTableName('m2e_amcf_config');

        if (!$connection->isTableExists($mcfConfigTable)) {
            return;
        }

        $mcfConfigs = $connection->fetchAll(
            $connection->select()->from($mcfConfigTable)
        );
        $coreConfigModifier = $this->getConfigModifier(
            \M2E\AmazonMcf\Helper\Module::IDENTIFIER
        );

        foreach ($mcfConfigs as $mcfConfig) {
            $this->migrate(
                $mcfConfig['group'],
                $mcfConfig['key'],
                $mcfConfig['value'],
                $coreConfigModifier
            );
        }

        $this->getConnection()
             ->dropTable($mcfConfigTable);
    }

    private function migrate(
        string $group,
        string $key,
        string $value,
        \M2E\Core\Model\Setup\Database\Modifier\Config $coreConfigModifier
    ): void {
        // migrate 'is_enabled' to 'is_disabled', as in the core
        if (
            $group === '/'
            && $key === 'is_enabled'
        ) {
            $key = 'is_disabled';
            $value = $value === '1' ? '0' : '1';
        }

        $coreConfigModifier->getEntity($group, $key)
                           ->insert($value);
    }
}
