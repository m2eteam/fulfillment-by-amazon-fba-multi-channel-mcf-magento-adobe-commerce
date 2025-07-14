<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Helper\Module\Database;

class Tables
{
    public const PREFIX = 'm2e_amcf_';

    public const TABLE_NAME_SYSTEM_LOG = self::PREFIX . 'system_log';
    public const TABLE_NAME_OPERATION_HISTORY = self::PREFIX . 'operation_history';

    public const TABLE_NAME_LOCK_ITEM = self::PREFIX . 'lock_item';
    public const TABLE_NAME_LOCK_TRANSACTIONAL = self::PREFIX . 'lock_transactional';

    public const TABLE_NAME_ACCOUNT = self::PREFIX . 'account';

    public const TABLE_NAME_PRODUCT = self::PREFIX . 'product';
    public const TABLE_NAME_PRODUCT_LOG = self::PREFIX . 'product_log';

    public const TABLE_NAME_ORDER = self::PREFIX . 'order';
    public const TABLE_NAME_ORDER_ITEM = self::PREFIX . 'order_item';
    public const TABLE_NAME_ORDER_LOG = self::PREFIX . 'order_log';

    private static function getTablesModels(): array
    {
        return [
            self::TABLE_NAME_SYSTEM_LOG => \M2E\AmazonMcf\Model\SystemLog::class,
            self::TABLE_NAME_OPERATION_HISTORY => \M2E\AmazonMcf\Model\OperationHistory::class,
            self::TABLE_NAME_LOCK_ITEM => \M2E\AmazonMcf\Model\Lock\Item::class,
            self::TABLE_NAME_LOCK_TRANSACTIONAL => \M2E\AmazonMcf\Model\Lock\Transactional::class,
            self::TABLE_NAME_ACCOUNT => \M2E\AmazonMcf\Model\Account::class,
            self::TABLE_NAME_PRODUCT => \M2E\AmazonMcf\Model\Product::class,
            self::TABLE_NAME_PRODUCT_LOG => \M2E\AmazonMcf\Model\Product\Log::class,
            self::TABLE_NAME_ORDER => \M2E\AmazonMcf\Model\Order::class,
            self::TABLE_NAME_ORDER_ITEM => \M2E\AmazonMcf\Model\Order\Item::class,
            self::TABLE_NAME_ORDER_LOG => \M2E\AmazonMcf\Model\Order\Log::class,
        ];
    }

    private static function getTablesResourcesModels(): array
    {
        return [
            self::TABLE_NAME_SYSTEM_LOG => \M2E\AmazonMcf\Model\ResourceModel\SystemLog::class,
            self::TABLE_NAME_OPERATION_HISTORY => \M2E\AmazonMcf\Model\ResourceModel\OperationHistory::class,
            self::TABLE_NAME_LOCK_ITEM => \M2E\AmazonMcf\Model\ResourceModel\Lock\Item::class,
            self::TABLE_NAME_LOCK_TRANSACTIONAL => \M2E\AmazonMcf\Model\ResourceModel\Lock\Transactional::class,
            self::TABLE_NAME_ACCOUNT => \M2E\AmazonMcf\Model\ResourceModel\Account::class,
            self::TABLE_NAME_PRODUCT => \M2E\AmazonMcf\Model\ResourceModel\Product::class,
            self::TABLE_NAME_PRODUCT_LOG => \M2E\AmazonMcf\Model\ResourceModel\Product\Log::class,
            self::TABLE_NAME_ORDER => \M2E\AmazonMcf\Model\ResourceModel\Order::class,
            self::TABLE_NAME_ORDER_ITEM => \M2E\AmazonMcf\Model\ResourceModel\Order\Item::class,
            self::TABLE_NAME_ORDER_LOG => \M2E\AmazonMcf\Model\ResourceModel\Order\Log::class,
        ];
    }

    /**
     * @return string[]
     */
    public static function getAllTables(): array
    {
        return array_keys(self::getTablesResourcesModels());
    }

    public static function getTableModel(string $tableName): string
    {
        $tablesModels = self::getTablesModels();

        return $tablesModels[$tableName];
    }

    public static function getTableResourceModel(string $tableName): string
    {
        $tablesModels = self::getTablesResourcesModels();

        return $tablesModels[$tableName];
    }

    // ----------------------------------------

    public static function isModuleTable(string $tableName): bool
    {
        return strpos($tableName, self::PREFIX) !== false;
    }
}
