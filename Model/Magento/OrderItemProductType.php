<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Magento;

class OrderItemProductType
{
    private const SIMPLE = 'simple';
    private const CONFIGURABLE = 'configurable';

    public static function isSimple(string $productType): bool
    {
        return $productType === self::SIMPLE;
    }

    public static function isConfigurable(string $productType): bool
    {
        return $productType === self::CONFIGURABLE;
    }
}
