<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\ResourceModel;

class Account extends \M2E\AmazonMcf\Model\ResourceModel\AbstractModel
{
    public const COLUMN_ID = 'id';
    public const COLUMN_MERCHANT_ID = 'merchant_id';
    public const COLUMN_REGION = 'region';
    public const COLUMN_IS_ENABLED = 'is_enabled';
    public const COLUMN_CREATE_DATE = 'create_date';
    public const COLUMN_UPDATE_DATE = 'update_date';

    protected function _construct()
    {
        $this->_init(
            \M2E\AmazonMcf\Helper\Module\Database\Tables::TABLE_NAME_ACCOUNT,
            self::COLUMN_ID
        );
    }
}
