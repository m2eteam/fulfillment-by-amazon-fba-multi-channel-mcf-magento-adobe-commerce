<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Setup\InstallHandler;

use M2E\AmazonMcf\Helper\Module\Database\Tables as TablesHelper;
use M2E\AmazonMcf\Model\ResourceModel\Order as OrderResource;
use M2E\AmazonMcf\Model\ResourceModel\Order\Item as OrderItemResource;
use Magento\Framework\DB\Ddl\Table;

class OrderHandler implements \M2E\Core\Model\Setup\InstallHandlerInterface
{
    use \M2E\AmazonMcf\Setup\InstallHandler\HandlerTrait;

    public function installSchema(\Magento\Framework\Setup\SetupInterface $setup): void
    {
        $this->installOrderTable($setup);
        $this->installOrderItemTable($setup);
    }

    public function installOrderTable(\Magento\Framework\Setup\SetupInterface $setup): void
    {
        $orderTable = $setup->getConnection()
                            ->newTable(
                                $this->tablesHelper->getFullName(TablesHelper::TABLE_NAME_ORDER)
                            );

        $orderTable->addColumn(
            OrderResource::COLUMN_ID,
            Table::TYPE_INTEGER,
            null,
            [
                'unsigned' => true,
                'primary' => true,
                'nullable' => false,
                'auto_increment' => true,
            ]
        );
        $orderTable->addColumn(
            OrderResource::COLUMN_CHANNEL,
            Table::TYPE_TEXT,
            255
        );
        $orderTable->addColumn(
            OrderResource::COLUMN_CHANNEL_ORDER_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'default' => null]
        );
        $orderTable->addColumn(
            OrderResource::COLUMN_CHANNEL_EXTERNAL_ORDER_ID,
            Table::TYPE_TEXT,
            255,
            ['default' => null]
        );
        $orderTable->addColumn(
            OrderResource::COLUMN_MAGENTO_ORDER_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false]
        );
        $orderTable->addColumn(
            OrderResource::COLUMN_MAGENTO_ORDER_INCREMENT_ID,
            Table::TYPE_TEXT,
            50,
            ['nullable' => false]
        );
        $orderTable->addColumn(
            OrderResource::COLUMN_ACCOUNT_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => true]
        );
        $orderTable->addColumn(
            OrderResource::COLUMN_REGION,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false]
        );
        $orderTable->addColumn(
            OrderResource::COLUMN_STATUS,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false]
        );
        $orderTable->addColumn(
            OrderResource::COLUMN_IS_PAID,
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false]
        );
        $orderTable->addColumn(
            OrderResource::COLUMN_SELLER_FULFILLMENT_ID,
            Table::TYPE_TEXT,
            255,
            ['default' => null]
        );
        $orderTable->addColumn(
            OrderResource::COLUMN_STATUS_PROCESS_ATTEMPT_COUNT,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'default' => 0]
        );
        $orderTable->addColumn(
            OrderResource::COLUMN_STATUS_PROCESS_ATTEMPT_DATE,
            Table::TYPE_DATETIME,
            null,
            ['default' => null]
        );
        $orderTable->addColumn(
            OrderResource::COLUMN_QTY_RESERVATION_DATE,
            Table::TYPE_DATETIME,
            null,
            ['default' => null]
        );
        $orderTable->addColumn(
            OrderResource::COLUMN_CHANNEL_PURCHASE_DATE,
            Table::TYPE_DATETIME,
            null,
            ['default' => null]
        );
        $orderTable->addColumn(
            OrderResource::COLUMN_SHIPPING_DATE,
            Table::TYPE_DATETIME,
            null,
            ['default' => null]
        );
        $orderTable->addColumn(
            OrderResource::COLUMN_UPDATE_DATE,
            Table::TYPE_DATETIME,
            null,
            ['default' => null]
        );
        $orderTable->addColumn(
            OrderResource::COLUMN_CREATE_DATE,
            Table::TYPE_DATETIME,
            null,
            ['default' => null]
        );
        $orderTable->addIndex(OrderResource::COLUMN_CHANNEL, OrderResource::COLUMN_CHANNEL);
        $orderTable->addIndex(OrderResource::COLUMN_ACCOUNT_ID, OrderResource::COLUMN_ACCOUNT_ID);
        $orderTable->addIndex(
            OrderResource::COLUMN_CHANNEL_ORDER_ID . '__' . OrderResource::COLUMN_MAGENTO_ORDER_ID,
            [OrderResource::COLUMN_CHANNEL_ORDER_ID, OrderResource::COLUMN_MAGENTO_ORDER_ID],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        );
        $orderTable->setOption('type', 'INNODB')
                   ->setOption('charset', 'utf8')
                   ->setOption('collate', 'utf8_general_ci')
                   ->setOption('row_format', 'dynamic');

        $setup->getConnection()
              ->createTable($orderTable);
    }

    public function installOrderItemTable(\Magento\Framework\Setup\SetupInterface $setup): void
    {
        $orderItemTable = $setup->getConnection()
                                ->newTable(
                                    $this->tablesHelper->getFullName(TablesHelper::TABLE_NAME_ORDER_ITEM)
                                );

        $orderItemTable->addColumn(
            OrderItemResource::COLUMN_ID,
            Table::TYPE_INTEGER,
            null,
            [
                'unsigned' => true,
                'primary' => true,
                'nullable' => false,
                'auto_increment' => true,
            ]
        );
        $orderItemTable->addColumn(
            OrderItemResource::COLUMN_ORDER_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false]
        );
        $orderItemTable->addColumn(
            OrderItemResource::COLUMN_PRODUCT_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false]
        );
        $orderItemTable->addColumn(
            OrderItemResource::COLUMN_MAGENTO_ORDER_ITEM_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false]
        );
        $orderItemTable->addColumn(
            OrderItemResource::COLUMN_SELLER_FULFILLMENT_ITEM_ID,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false]
        );
        $orderItemTable->addColumn(
            OrderItemResource::COLUMN_CHANNEL_SKU,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false]
        );
        $orderItemTable->addColumn(
            OrderItemResource::COLUMN_QTY,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false]
        );
        $orderItemTable->addColumn(
            OrderItemResource::COLUMN_PACKAGE_NUMBER,
            Table::TYPE_INTEGER,
            32,
            ['unsigned' => true, 'default' => null]
        );
        $orderItemTable->addColumn(
            OrderItemResource::COLUMN_TRACKING_NUMBER,
            Table::TYPE_TEXT,
            255,
            ['default' => null]
        );
        $orderItemTable->addColumn(
            OrderItemResource::COLUMN_CARRIER_CODE,
            Table::TYPE_TEXT,
            255,
            ['default' => null]
        );
        $orderItemTable->addColumn(
            OrderItemResource::COLUMN_CARRIER_URL,
            Table::TYPE_TEXT,
            255,
            ['default' => null]
        );
        $orderItemTable->addColumn(
            OrderItemResource::COLUMN_UPDATE_DATE,
            Table::TYPE_DATETIME,
            null,
            ['default' => null]
        );
        $orderItemTable->addColumn(
            OrderItemResource::COLUMN_CREATE_DATE,
            Table::TYPE_DATETIME,
            null,
            ['default' => null]
        );
        $orderItemTable->addIndex(
            OrderItemResource::COLUMN_ORDER_ID,
            OrderItemResource::COLUMN_ORDER_ID
        );
        $orderItemTable->addIndex(
            OrderItemResource::COLUMN_PRODUCT_ID,
            OrderItemResource::COLUMN_PRODUCT_ID
        );
        $orderItemTable->addIndex(
            OrderItemResource::COLUMN_MAGENTO_ORDER_ITEM_ID,
            OrderItemResource::COLUMN_MAGENTO_ORDER_ITEM_ID
        );
        $orderItemTable->addIndex(
            OrderItemResource::COLUMN_SELLER_FULFILLMENT_ITEM_ID,
            OrderItemResource::COLUMN_SELLER_FULFILLMENT_ITEM_ID
        );
        $orderItemTable->setOption('type', 'INNODB')
                       ->setOption('charset', 'utf8')
                       ->setOption('collate', 'utf8_general_ci')
                       ->setOption('row_format', 'dynamic');

        $setup->getConnection()
              ->createTable($orderItemTable);
    }

    public function installData(\Magento\Framework\Setup\SetupInterface $setup): void
    {
    }
}
