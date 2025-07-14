<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Setup\Update;

class Config implements \M2E\Core\Model\Setup\Upgrade\Entity\ConfigInterface
{
    public function getFeaturesList(): array
    {
        return [
            'y24_m06' => [
                \M2E\AmazonMcf\Setup\Update\y24_m06\AddAsinColumnToProduct::class,
                \M2E\AmazonMcf\Setup\Update\y24_m06\AddChannelPurchaseDateColumnToOrder::class,
            ],
            'y24_m07' => [
                \M2E\AmazonMcf\Setup\Update\y24_m07\AddChannelsToConfig::class,
                \M2E\AmazonMcf\Setup\Update\y24_m07\ModifyChannelOrderIdInOrder::class,
            ],
            'y24_m11' => [
                \M2E\AmazonMcf\Setup\Update\y24_m11\AddTikTokShopChannel::class,
            ],
            'y24_m12' => [
                \M2E\AmazonMcf\Setup\Update\y24_m12\AddCarrierCodeToOrderItem::class,
                \M2E\AmazonMcf\Setup\Update\y24_m12\AddChannelExternalOrderId::class,
            ],
            'y25_m03' => [
                \M2E\AmazonMcf\Setup\Update\y25_m03\AddKauflandChannel::class,
            ],
            'y25_m05' => [
                \M2E\AmazonMcf\Setup\Update\y25_m05\AddOnBuyChannel::class,
                \M2E\AmazonMcf\Setup\Update\y25_m05\AddOttoChannel::class,
                \M2E\AmazonMcf\Setup\Update\y25_m05\AddTemuChannel::class,
                \M2E\AmazonMcf\Setup\Update\y25_m05\MigrateConfigToCore::class,
                \M2E\AmazonMcf\Setup\Update\y25_m05\MigrateRegistryToCore::class,
                \M2E\AmazonMcf\Setup\Update\y25_m05\AddWalmartChannel::class,
            ],
        ];
    }
}
