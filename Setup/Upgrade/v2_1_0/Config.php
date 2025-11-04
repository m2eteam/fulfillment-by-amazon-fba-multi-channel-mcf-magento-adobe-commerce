<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Setup\Upgrade\v2_1_0;

class Config implements \M2E\Core\Model\Setup\Upgrade\Entity\ConfigInterface
{
    public function getFeaturesList(): array
    {
        return [
            \M2E\AmazonMcf\Setup\Update\y25_m10\AddCarrierUrl::class,
        ];
    }
}
