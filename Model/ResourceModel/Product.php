<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\ResourceModel;

class Product extends \M2E\AmazonMcf\Model\ResourceModel\AbstractModel
{
    public const COLUMN_ID = 'id';
    public const COLUMN_ACCOUNT_ID = 'account_id';
    public const COLUMN_CHANNEL_SKU = 'channel_sku';
    public const COLUMN_ASIN = 'asin';
    public const COLUMN_MAGENTO_PRODUCT_ID = 'magento_product_id';
    public const COLUMN_MAGENTO_PRODUCT_SKU = 'magento_product_sku';
    public const COLUMN_QTY = 'qty';
    public const COLUMN_IS_ENABLED = 'is_enabled';
    public const COLUMN_QTY_LAST_UPDATE_DATE = 'qty_last_update_date';
    public const COLUMN_CREATE_DATE = 'create_date';

    protected function _construct()
    {
        $this->_init(
            \M2E\AmazonMcf\Helper\Module\Database\Tables::TABLE_NAME_PRODUCT,
            self::COLUMN_ID
        );
    }
}
