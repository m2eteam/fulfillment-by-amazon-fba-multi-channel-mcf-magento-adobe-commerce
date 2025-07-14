<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Setup\Update\y24_m06;

class AddChannelPurchaseDateColumnToOrder extends \M2E\Core\Model\Setup\Upgrade\Entity\AbstractFeature
{
    public function execute(): void
    {
        $this->createTableModifier(\M2E\AmazonMcf\Helper\Module\Database\Tables::TABLE_NAME_ORDER)
             ->addColumn(
                 \M2E\AmazonMcf\Model\ResourceModel\Order::COLUMN_CHANNEL_PURCHASE_DATE,
                 'DATETIME',
                 null,
                 \M2E\AmazonMcf\Model\ResourceModel\Order::COLUMN_QTY_RESERVATION_DATE
             );
    }
}
