<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\ResourceModel;

class Order extends \M2E\AmazonMcf\Model\ResourceModel\AbstractModel
{
    public const COLUMN_ID = 'id';
    public const COLUMN_CHANNEL = 'channel';
    public const COLUMN_CHANNEL_ORDER_ID = 'channel_order_id';
    public const COLUMN_CHANNEL_EXTERNAL_ORDER_ID = 'channel_external_order_id';
    public const COLUMN_MAGENTO_ORDER_ID = 'magento_order_id';
    public const COLUMN_MAGENTO_ORDER_INCREMENT_ID = 'magento_order_increment_id';
    public const COLUMN_ACCOUNT_ID = 'account_id';
    public const COLUMN_REGION = 'region';
    public const COLUMN_STATUS = 'status';
    public const COLUMN_IS_PAID = 'is_paid';
    public const COLUMN_SELLER_FULFILLMENT_ID = 'seller_fulfillment_id';
    public const COLUMN_STATUS_PROCESS_ATTEMPT_COUNT = 'status_process_attempt_count';
    public const COLUMN_STATUS_PROCESS_ATTEMPT_DATE = 'status_process_attempt_date';
    public const COLUMN_QTY_RESERVATION_DATE = 'qty_reservation_date';
    public const COLUMN_CHANNEL_PURCHASE_DATE = 'channel_purchase_date';
    public const COLUMN_SHIPPING_DATE = 'shipping_date';
    public const COLUMN_UPDATE_DATE = 'update_date';
    public const COLUMN_CREATE_DATE = 'create_date';

    protected function _construct()
    {
        $this->_init(
            \M2E\AmazonMcf\Helper\Module\Database\Tables::TABLE_NAME_ORDER,
            self::COLUMN_ID
        );
    }
}
