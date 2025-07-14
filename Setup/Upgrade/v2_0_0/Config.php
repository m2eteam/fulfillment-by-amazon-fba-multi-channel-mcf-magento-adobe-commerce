<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Setup\Upgrade\v2_0_0;

class Config implements \M2E\Core\Model\Setup\Upgrade\Entity\ConfigInterface
{
    public function getFeaturesList(): array
    {
        return [
            \M2E\AmazonMcf\Setup\Update\y25_m05\MigrateConfigToCore::class,
            \M2E\AmazonMcf\Setup\Update\y25_m05\MigrateRegistryToCore::class,
        ];
    }
}
