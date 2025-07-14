<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Setup\Upgrade\v1_0_3;

class Config implements \M2E\Core\Model\Setup\Upgrade\Entity\ConfigInterface
{
    public function getFeaturesList(): array
    {
        return [
            \M2E\AmazonMcf\Setup\Update\y24_m06\AddAsinColumnToProduct::class,
            \M2E\AmazonMcf\Setup\Update\y24_m06\AddChannelPurchaseDateColumnToOrder::class,
        ];
    }
}
