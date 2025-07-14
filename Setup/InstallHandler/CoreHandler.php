<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Setup\InstallHandler;

use M2E\AmazonMcf\Helper\Module\Database\Tables as TablesHelper;
use M2E\AmazonMcf\Model\ResourceModel\Lock\Item as LockItemResource;
use M2E\AmazonMcf\Model\ResourceModel\Lock\Transactional as LockTransactionalResource;
use Magento\Framework\DB\Ddl\Table;

class CoreHandler implements \M2E\Core\Model\Setup\InstallHandlerInterface
{
    private \M2E\Core\Helper\Module\Database\Tables $tablesHelper;
    private \M2E\Core\Model\Setup\Database\Modifier\ConfigFactory $configFactory;

    public function __construct(
        \M2E\Core\Helper\Module\Database\Tables $tablesHelper,
        \M2E\Core\Model\Setup\Database\Modifier\ConfigFactory $configFactory
    ) {
        $this->tablesHelper = $tablesHelper;
        $this->configFactory = $configFactory;
    }

    public function installSchema(\Magento\Framework\Setup\SetupInterface $setup): void
    {
        $this->installOperationHistoryTable($setup);
        $this->installLockItemTable($setup);
        $this->installLockTransactionTable($setup);
    }

    private function installOperationHistoryTable(\Magento\Framework\Setup\SetupInterface $setup): void
    {
        $operationHistory = $setup->getConnection()
                                  ->newTable(
                                      $this->tablesHelper->getFullName(TablesHelper::TABLE_NAME_OPERATION_HISTORY)
                                  );

        $operationHistory->addColumn(
            'id',
            Table::TYPE_INTEGER,
            null,
            [
                'unsigned' => true,
                'primary' => true,
                'nullable' => false,
                'auto_increment' => true,
            ]
        );
        $operationHistory->addColumn(
            'nick',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false]
        );
        $operationHistory->addColumn(
            'parent_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'default' => null]
        );
        $operationHistory->addColumn(
            'initiator',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => 0]
        );
        $operationHistory->addColumn(
            'start_date',
            Table::TYPE_DATETIME,
            null,
            ['nullable' => false]
        );
        $operationHistory->addColumn(
            'end_date',
            Table::TYPE_DATETIME,
            null,
            ['default' => null]
        );
        $operationHistory->addColumn(
            'data',
            Table::TYPE_TEXT,
            null,
            ['default' => null]
        );
        $operationHistory->addColumn(
            'update_date',
            Table::TYPE_DATETIME,
            null,
            ['default' => null]
        );
        $operationHistory->addColumn(
            'create_date',
            Table::TYPE_DATETIME,
            null,
            ['default' => null]
        );
        $operationHistory->addIndex('nick', 'nick');
        $operationHistory->addIndex('parent_id', 'parent_id');
        $operationHistory->addIndex('initiator', 'initiator');
        $operationHistory->addIndex('start_date', 'start_date');
        $operationHistory->addIndex('end_date', 'end_date');
        $operationHistory->setOption('type', 'INNODB')
                         ->setOption('charset', 'utf8')
                         ->setOption('collate', 'utf8_general_ci')
                         ->setOption('row_format', 'dynamic');

        $setup->getConnection()
              ->createTable($operationHistory);
    }

    private function installLockItemTable(\Magento\Framework\Setup\SetupInterface $setup): void
    {
        $lockItemTable = $setup->getConnection()
                               ->newTable(
                                   $this->tablesHelper->getFullName(TablesHelper::TABLE_NAME_LOCK_ITEM)
                               );

        $lockItemTable->addColumn(
            LockItemResource::COLUMN_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'primary' => true, 'nullable' => false, 'auto_increment' => true]
        );
        $lockItemTable->addColumn(
            LockItemResource::COLUMN_NICK,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false]
        );
        $lockItemTable->addColumn(
            LockItemResource::COLUMN_PARENT_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'default' => null]
        );
        $lockItemTable->addColumn(
            LockItemResource::COLUMN_DATA,
            Table::TYPE_TEXT,
            null,
            ['default' => null]
        );
        $lockItemTable->addColumn(
            LockItemResource::COLUMN_UPDATE_DATE,
            Table::TYPE_DATETIME,
            null,
            ['default' => null]
        );
        $lockItemTable->addColumn(
            LockItemResource::COLUMN_CREATE_DATE,
            Table::TYPE_DATETIME,
            null,
            ['default' => null]
        );
        $lockItemTable->addIndex('nick', LockItemResource::COLUMN_NICK);
        $lockItemTable->addIndex('parent_id', LockItemResource::COLUMN_PARENT_ID);
        $lockItemTable->setOption('type', 'INNODB')
                      ->setOption('charset', 'utf8')
                      ->setOption('collate', 'utf8_general_ci')
                      ->setOption('row_format', 'dynamic');

        $setup->getConnection()
              ->createTable($lockItemTable);
    }

    private function installLockTransactionTable(\Magento\Framework\Setup\SetupInterface $setup): void
    {
        $lockTransactional = $setup->getConnection()
                                   ->newTable(
                                       $this->tablesHelper->getFullName(TablesHelper::TABLE_NAME_LOCK_TRANSACTIONAL)
                                   );

        $lockTransactional->addColumn(
            LockTransactionalResource::COLUMN_ID,
            Table::TYPE_INTEGER,
            null,
            [
                'unsigned' => true,
                'primary' => true,
                'nullable' => false,
                'auto_increment' => true,
            ]
        );
        $lockTransactional->addColumn(
            LockTransactionalResource::COLUMN_NICK,
            Table::TYPE_TEXT,
            255,
            ['nullable' => false]
        );
        $lockTransactional->addColumn(
            LockTransactionalResource::COLUMN_CREATE_DATE,
            Table::TYPE_DATETIME,
            null,
            ['default' => null]
        );
        $lockTransactional->addIndex('nick', LockTransactionalResource::COLUMN_NICK);
        $lockTransactional->setOption('type', 'INNODB')
                          ->setOption('charset', 'utf8')
                          ->setOption('collate', 'utf8_general_ci')
                          ->setOption('row_format', 'dynamic');

        $setup->getConnection()
              ->createTable($lockTransactional);
    }

    public function installData(\Magento\Framework\Setup\SetupInterface $setup): void
    {
        $this->installConfigData($setup);
    }

    private function installConfigData(\Magento\Framework\Setup\SetupInterface $setup): void
    {
        $config = $this->configFactory->create(
            \M2E\AmazonMcf\Helper\Module::IDENTIFIER,
            $setup
        );

        $config->insert('/', 'is_disabled', '0');
        $config->insert('/', 'environment', 'production');
        $config->insert('/cron/', 'is_enabled', '1');

        $config->insert('/account/force_sync/', 'is_enabled', '1');
        $config->insert('/channel/ebay/', 'is_enabled', '1');
        $config->insert('/channel/walmart/', 'is_enabled', '1');
        $config->insert('/channel/magento/', 'is_enabled', '1');
        $config->insert('/channel/tts/', 'is_enabled', '1');
        $config->insert('/channel/kaufland/', 'is_enabled', '1');
        $config->insert('/channel/onbuy/', 'is_enabled', '1');
        $config->insert('/channel/otto/', 'is_enabled', '1');
        $config->insert('/channel/temu/', 'is_enabled', '1');
    }
}
