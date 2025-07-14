<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\ResourceModel;

class OperationHistory extends \M2E\AmazonMcf\Model\ResourceModel\AbstractModel
{
    public const COLUMN_ID = 'id';

    public function _construct(): void
    {
        $this->_init(
            \M2E\AmazonMcf\Helper\Module\Database\Tables::TABLE_NAME_OPERATION_HISTORY,
            self::COLUMN_ID
        );
    }
}
