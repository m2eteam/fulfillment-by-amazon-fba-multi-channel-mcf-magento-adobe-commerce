<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Setup\Upgrade\v1_5_0;

class Config implements \M2E\Core\Model\Setup\Upgrade\Entity\ConfigInterface
{
    public function getFeaturesList(): array
    {
        return [
            \M2E\AmazonMcf\Setup\Update\y25_m05\AddOttoChannel::class,
            \M2E\AmazonMcf\Setup\Update\y25_m05\AddOnBuyChannel::class,
            \M2E\AmazonMcf\Setup\Update\y25_m05\AddTemuChannel::class,
        ];
    }
}
