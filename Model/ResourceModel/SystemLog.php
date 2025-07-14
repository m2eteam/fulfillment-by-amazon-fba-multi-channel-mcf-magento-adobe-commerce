<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\ResourceModel;

class SystemLog extends \M2E\AmazonMcf\Model\ResourceModel\AbstractModel
{
    public const COLUMN_ID = 'id';
    public const COLUMN_SEVERITY = 'severity';
    public const COLUMN_MESSAGE = 'message';
    public const COLUMN_CONTEXT = 'context';
    public const COLUMN_CREATE_DATE = 'create_date';

    public function _construct(): void
    {
        $this->_init(
            \M2E\AmazonMcf\Helper\Module\Database\Tables::TABLE_NAME_SYSTEM_LOG,
            self::COLUMN_ID
        );
    }
}
