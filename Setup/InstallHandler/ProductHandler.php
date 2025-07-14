<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Setup\InstallHandler;

use M2E\AmazonMcf\Helper\Module\Database\Tables as TablesHelper;
use M2E\AmazonMcf\Model\ResourceModel\Product as ProductResource;
use Magento\Framework\DB\Ddl\Table;

class ProductHandler implements \M2E\Core\Model\Setup\InstallHandlerInterface
{
    use \M2E\AmazonMcf\Setup\InstallHandler\HandlerTrait;

    public function installSchema(\Magento\Framework\Setup\SetupInterface $setup): void
    {
        $this->installProductTable($setup);
    }

    public function installProductTable(\Magento\Framework\Setup\SetupInterface $setup): void
    {
        $productTable = $setup->getConnection()
                              ->newTable(
                                  $this->tablesHelper->getFullName(TablesHelper::TABLE_NAME_PRODUCT)
                              );

        $productTable->addColumn(
            ProductResource::COLUMN_ID,
            Table::TYPE_INTEGER,
            null,
            [
                'unsigned' => true,
                'primary' => true,
                'nullable' => false,
                'auto_increment' => true,
            ]
        );
        $productTable->addColumn(
            ProductResource::COLUMN_ACCOUNT_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false]
        );
        $productTable->addColumn(
            ProductResource::COLUMN_CHANNEL_SKU,
            Table::TYPE_TEXT,
            255
        );
        $productTable->addColumn(
            ProductResource::COLUMN_ASIN,
            Table::TYPE_TEXT,
            255,
            ['default' => null]
        );
        $productTable->addColumn(
            ProductResource::COLUMN_MAGENTO_PRODUCT_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false]
        );
        $productTable->addColumn(
            ProductResource::COLUMN_MAGENTO_PRODUCT_SKU,
            Table::TYPE_TEXT,
            255
        );
        $productTable->addColumn(
            ProductResource::COLUMN_QTY,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false]
        );
        $productTable->addColumn(
            ProductResource::COLUMN_IS_ENABLED,
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false]
        );
        $productTable->addColumn(
            ProductResource::COLUMN_QTY_LAST_UPDATE_DATE,
            Table::TYPE_DATETIME,
            null,
            ['default' => null]
        );
        $productTable->addColumn(
            ProductResource::COLUMN_CREATE_DATE,
            Table::TYPE_DATETIME,
            null,
            ['default' => null]
        );
        $productTable->addIndex(
            ProductResource::COLUMN_ACCOUNT_ID,
            ProductResource::COLUMN_ACCOUNT_ID
        );
        $productTable->addIndex(
            ProductResource::COLUMN_CHANNEL_SKU,
            ProductResource::COLUMN_CHANNEL_SKU
        );
        $productTable->addIndex(
            ProductResource::COLUMN_MAGENTO_PRODUCT_ID,
            ProductResource::COLUMN_MAGENTO_PRODUCT_ID
        );
        $productTable->addIndex(
            ProductResource::COLUMN_MAGENTO_PRODUCT_SKU,
            ProductResource::COLUMN_MAGENTO_PRODUCT_SKU
        );
        $productTable->setOption('type', 'INNODB')
                     ->setOption('charset', 'utf8')
                     ->setOption('collate', 'utf8_general_ci')
                     ->setOption('row_format', 'dynamic');

        $setup->getConnection()
              ->createTable($productTable);
    }

    public function installData(\Magento\Framework\Setup\SetupInterface $setup): void
    {
    }
}
