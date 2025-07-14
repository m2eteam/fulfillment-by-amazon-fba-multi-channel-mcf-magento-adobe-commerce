<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Setup\Update\y24_m06;

class AddAsinColumnToProduct extends \M2E\Core\Model\Setup\Upgrade\Entity\AbstractFeature
{
    public function execute(): void
    {
        $this->createTableModifier(\M2E\AmazonMcf\Helper\Module\Database\Tables::TABLE_NAME_PRODUCT)
             ->addColumn(
                 \M2E\AmazonMcf\Model\ResourceModel\Product::COLUMN_ASIN,
                 'VARCHAR(255)',
                 null,
                 \M2E\AmazonMcf\Model\ResourceModel\Product::COLUMN_CHANNEL_SKU
             );
    }
}
