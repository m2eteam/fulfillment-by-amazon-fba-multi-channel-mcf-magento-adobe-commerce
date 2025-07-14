<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Setup\Update\y25_m05;

class AddWalmartChannel extends \M2E\Core\Model\Setup\Upgrade\Entity\AbstractFeature
{
    public function execute(): void
    {
        $configModifier = $this->getConfigModifier(
            \M2E\AmazonMcf\Helper\Module::IDENTIFIER
        );

        $configModifier->insert(
            '/channel/walmart/',
            'is_enabled',
            '1'
        );
    }
}
