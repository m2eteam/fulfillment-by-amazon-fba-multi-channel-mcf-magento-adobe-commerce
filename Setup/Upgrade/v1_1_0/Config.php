<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Setup\Upgrade\v1_1_0;

class Config implements \M2E\Core\Model\Setup\Upgrade\Entity\ConfigInterface
{
    public function getFeaturesList(): array
    {
        return [
            \M2E\AmazonMcf\Setup\Update\y24_m07\AddChannelsToConfig::class,
            \M2E\AmazonMcf\Setup\Update\y24_m07\ModifyChannelOrderIdInOrder::class,
        ];
    }
}
