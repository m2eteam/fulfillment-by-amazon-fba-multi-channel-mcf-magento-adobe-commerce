<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Setup\Update\y25_m03;

class AddKauflandChannel extends \M2E\Core\Model\Setup\Upgrade\Entity\AbstractFeature
{
    public function execute(): void
    {
        $configModifier = $this->getConfigModifier(
            \M2E\AmazonMcf\Helper\Module::IDENTIFIER
        );

        $configModifier->insert(
            '/channel/kaufland/',
            'is_enabled',
            '1'
        );
    }
}
