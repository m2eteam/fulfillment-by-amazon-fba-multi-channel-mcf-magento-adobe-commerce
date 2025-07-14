<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Setup\InstallHandler;

use M2E\AmazonMcf\Helper\Module\Database\Tables as TablesHelper;
use M2E\AmazonMcf\Model\ResourceModel\Account as AccountResource;
use Magento\Framework\DB\Ddl\Table;

class AccountHandler implements \M2E\Core\Model\Setup\InstallHandlerInterface
{
    use \M2E\AmazonMcf\Setup\InstallHandler\HandlerTrait;

    public function installSchema(\Magento\Framework\Setup\SetupInterface $setup): void
    {
        $this->installAccountTable($setup);
    }

    private function installAccountTable(\Magento\Framework\Setup\SetupInterface $setup): void
    {
        $accountTable = $setup->getConnection()
                              ->newTable(
                                  $this->tablesHelper->getFullName(TablesHelper::TABLE_NAME_ACCOUNT)
                              );

        $accountTable->addColumn(
            AccountResource::COLUMN_ID,
            Table::TYPE_INTEGER,
            null,
            [
                'unsigned' => true,
                'primary' => true,
                'nullable' => false,
                'auto_increment' => true,
            ]
        );
        $accountTable->addColumn(
            AccountResource::COLUMN_MERCHANT_ID,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false]
        );
        $accountTable->addColumn(
            AccountResource::COLUMN_REGION,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false]
        );
        $accountTable->addColumn(
            AccountResource::COLUMN_IS_ENABLED,
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false]
        );
        $accountTable->addColumn(
            AccountResource::COLUMN_UPDATE_DATE,
            Table::TYPE_DATETIME,
            null,
            ['default' => null]
        );
        $accountTable->addColumn(
            AccountResource::COLUMN_CREATE_DATE,
            Table::TYPE_DATETIME,
            null,
            ['default' => null]
        );
        $accountTable->addIndex(
            AccountResource::COLUMN_MERCHANT_ID,
            AccountResource::COLUMN_MERCHANT_ID,
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        );
        $accountTable->setOption('type', 'INNODB')
                     ->setOption('charset', 'utf8')
                     ->setOption('collate', 'utf8_general_ci')
                     ->setOption('row_format', 'dynamic');

        $setup->getConnection()
              ->createTable($accountTable);
    }

    public function installData(\Magento\Framework\Setup\SetupInterface $setup): void
    {
    }
}
