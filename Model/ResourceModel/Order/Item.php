<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\ResourceModel\Order;

class Item extends \M2E\AmazonMcf\Model\ResourceModel\AbstractModel
{
    public const COLUMN_ID = 'id';
    public const COLUMN_ORDER_ID = 'order_id';
    public const COLUMN_PRODUCT_ID = 'product_id';
    public const COLUMN_MAGENTO_ORDER_ITEM_ID = 'magento_order_item_id';
    public const COLUMN_SELLER_FULFILLMENT_ITEM_ID = 'seller_fulfillment_item_id';
    public const COLUMN_CHANNEL_SKU = 'channel_sku';
    public const COLUMN_QTY = 'qty';
    public const COLUMN_PACKAGE_NUMBER = 'package_number';
    public const COLUMN_TRACKING_NUMBER = 'tracking_number';
    public const COLUMN_CARRIER_CODE = 'carrier_code';
    public const COLUMN_CARRIER_URL = 'carrier_url';
    public const COLUMN_UPDATE_DATE = 'update_date';
    public const COLUMN_CREATE_DATE = 'create_date';

    protected function _construct()
    {
        $this->_init(
            \M2E\AmazonMcf\Helper\Module\Database\Tables::TABLE_NAME_ORDER_ITEM,
            self::COLUMN_ID
        );
    }
}
