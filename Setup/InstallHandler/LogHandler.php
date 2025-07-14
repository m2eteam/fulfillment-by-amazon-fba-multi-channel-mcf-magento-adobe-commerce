<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Setup\InstallHandler;

use M2E\AmazonMcf\Helper\Module\Database\Tables as TablesHelper;
use M2E\AmazonMcf\Model\ResourceModel\Order\Log as OrderLogResource;
use M2E\AmazonMcf\Model\ResourceModel\Product\Log as ProductLogResource;
use M2E\AmazonMcf\Model\ResourceModel\SystemLog as SystemLogResource;
use Magento\Framework\DB\Ddl\Table;

class LogHandler implements \M2E\Core\Model\Setup\InstallHandlerInterface
{
    use \M2E\AmazonMcf\Setup\InstallHandler\HandlerTrait;

    public function installSchema(\Magento\Framework\Setup\SetupInterface $setup): void
    {
        $this->installSystemLogTable($setup);
        $this->installProductLogTable($setup);
        $this->installOrderLogTable($setup);
    }

    public function installSystemLogTable(\Magento\Framework\Setup\SetupInterface $setup): void
    {
        $systemLogTable = $setup->getConnection()
                                ->newTable(
                                    $this->tablesHelper->getFullName(TablesHelper::TABLE_NAME_SYSTEM_LOG)
                                );

        $systemLogTable->addColumn(
            SystemLogResource::COLUMN_ID,
            Table::TYPE_INTEGER,
            null,
            [
                'unsigned' => true,
                'primary' => true,
                'nullable' => false,
                'auto_increment' => true,
            ]
        );
        $systemLogTable->addColumn(
            SystemLogResource::COLUMN_SEVERITY,
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false,]
        );
        $systemLogTable->addColumn(
            SystemLogResource::COLUMN_MESSAGE,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false]
        );
        $systemLogTable->addColumn(
            SystemLogResource::COLUMN_CONTEXT,
            Table::TYPE_TEXT,
            \M2E\Core\Model\ResourceModel\Setup::LONG_COLUMN_SIZE,
            ['default' => null]
        );
        $systemLogTable->addColumn(
            SystemLogResource::COLUMN_CREATE_DATE,
            Table::TYPE_DATETIME,
            null,
            ['nullable' => false]
        );
        $systemLogTable->addIndex(
            SystemLogResource::COLUMN_SEVERITY,
            SystemLogResource::COLUMN_SEVERITY
        );
        $systemLogTable->setOption('type', 'INNODB')
                       ->setOption('charset', 'utf8')
                       ->setOption('collate', 'utf8_general_ci')
                       ->setOption('row_format', 'dynamic');

        $setup->getConnection()
              ->createTable($systemLogTable);
    }

    public function installProductLogTable(\Magento\Framework\Setup\SetupInterface $setup): void
    {
        $productLogTable = $setup->getConnection()
                                 ->newTable(
                                     $this->tablesHelper->getFullName(TablesHelper::TABLE_NAME_PRODUCT_LOG)
                                 );

        $productLogTable->addColumn(
            ProductLogResource::COLUMN_ID,
            Table::TYPE_INTEGER,
            null,
            [
                'unsigned' => true,
                'primary' => true,
                'nullable' => false,
                'auto_increment' => true,
            ]
        );
        $productLogTable->addColumn(
            ProductLogResource::COLUMN_PRODUCT_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false]
        );
        $productLogTable->addColumn(
            ProductLogResource::COLUMN_SEVERITY,
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false,]
        );
        $productLogTable->addColumn(
            ProductLogResource::COLUMN_INITIATOR,
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false,]
        );
        $productLogTable->addColumn(
            ProductLogResource::COLUMN_MESSAGE,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false]
        );
        $productLogTable->addColumn(
            ProductLogResource::COLUMN_CONTEXT,
            Table::TYPE_TEXT,
            \M2E\Core\Model\ResourceModel\Setup::LONG_COLUMN_SIZE,
            ['default' => null]
        );
        $productLogTable->addColumn(
            ProductLogResource::COLUMN_CREATE_DATE,
            Table::TYPE_DATETIME,
            null,
            ['nullable' => false]
        );
        $productLogTable->addIndex(
            ProductLogResource::COLUMN_PRODUCT_ID,
            ProductLogResource::COLUMN_PRODUCT_ID
        );
        $productLogTable->addIndex(
            ProductLogResource::COLUMN_SEVERITY,
            ProductLogResource::COLUMN_SEVERITY
        );
        $productLogTable->addIndex(
            ProductLogResource::COLUMN_INITIATOR,
            ProductLogResource::COLUMN_INITIATOR
        );
        $productLogTable->setOption('type', 'INNODB')
                        ->setOption('charset', 'utf8')
                        ->setOption('collate', 'utf8_general_ci')
                        ->setOption('row_format', 'dynamic');

        $setup->getConnection()
              ->createTable($productLogTable);
    }

    public function installOrderLogTable(\Magento\Framework\Setup\SetupInterface $setup): void
    {
        $orderLogTable = $setup->getConnection()
                               ->newTable(
                                   $this->tablesHelper->getFullName(TablesHelper::TABLE_NAME_ORDER_LOG)
                               );

        $orderLogTable->addColumn(
            OrderLogResource::COLUMN_ID,
            Table::TYPE_INTEGER,
            null,
            [
                'unsigned' => true,
                'primary' => true,
                'nullable' => false,
                'auto_increment' => true,
            ]
        );
        $orderLogTable->addColumn(
            OrderLogResource::COLUMN_ORDER_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false]
        );
        $orderLogTable->addColumn(
            OrderLogResource::COLUMN_SEVERITY,
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false,]
        );
        $orderLogTable->addColumn(
            OrderLogResource::COLUMN_INITIATOR,
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false,]
        );
        $orderLogTable->addColumn(
            OrderLogResource::COLUMN_MESSAGE,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false]
        );
        $orderLogTable->addColumn(
            OrderLogResource::COLUMN_CONTEXT,
            Table::TYPE_TEXT,
            \M2E\Core\Model\ResourceModel\Setup::LONG_COLUMN_SIZE,
            ['default' => null]
        );
        $orderLogTable->addColumn(
            OrderLogResource::COLUMN_CREATE_DATE,
            Table::TYPE_DATETIME,
            null,
            ['nullable' => false]
        );
        $orderLogTable->addIndex(
            OrderLogResource::COLUMN_ORDER_ID,
            OrderLogResource::COLUMN_ORDER_ID
        );
        $orderLogTable->addIndex(
            OrderLogResource::COLUMN_SEVERITY,
            OrderLogResource::COLUMN_SEVERITY
        );
        $orderLogTable->addIndex(
            OrderLogResource::COLUMN_INITIATOR,
            OrderLogResource::COLUMN_INITIATOR
        );
        $orderLogTable->setOption('type', 'INNODB')
                      ->setOption('charset', 'utf8')
                      ->setOption('collate', 'utf8_general_ci')
                      ->setOption('row_format', 'dynamic');

        $setup->getConnection()
              ->createTable($orderLogTable);
    }

    public function installData(\Magento\Framework\Setup\SetupInterface $setup): void
    {
    }
}
