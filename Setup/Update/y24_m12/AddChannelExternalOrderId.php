<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Setup\Update\y24_m12;

class AddChannelExternalOrderId extends \M2E\Core\Model\Setup\Upgrade\Entity\AbstractFeature
{
    public function execute(): void
    {
        $this->createTableModifier(\M2E\AmazonMcf\Helper\Module\Database\Tables::TABLE_NAME_ORDER)
             ->addColumn(
                 \M2E\AmazonMcf\Model\ResourceModel\Order::COLUMN_CHANNEL_EXTERNAL_ORDER_ID,
                 'VARCHAR(255)',
                 null,
                 \M2E\AmazonMcf\Model\ResourceModel\Order::COLUMN_CHANNEL_ORDER_ID
             );
    }
}
