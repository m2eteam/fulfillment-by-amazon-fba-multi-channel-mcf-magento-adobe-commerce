<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Setup\Update\y24_m07;

class AddChannelsToConfig extends \M2E\Core\Model\Setup\Upgrade\Entity\AbstractFeature
{
    public function execute(): void
    {
        $configModifier = $this->getConfigModifier(
            \M2E\AmazonMcf\Helper\Module::IDENTIFIER
        );

        $configModifier->insert(
            '/channel/ebay/',
            'is_enabled',
            '1'
        );

        $configModifier->insert(
            '/channel/magento/',
            'is_enabled',
            '1'
        );
    }
}
