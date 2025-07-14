<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Setup;

class MagentoCoreConfigSettings implements \M2E\Core\Model\Setup\MagentoCoreConfigSettingsInterface
{
    public function getConfigKeyPrefix(): string
    {
        return \M2E\AmazonMcf\Helper\Module::MAGENTO_CONFIG_KEY_PREFIX;
    }
}
