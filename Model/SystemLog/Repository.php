<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\SystemLog;

class Repository
{
    private \M2E\AmazonMcf\Model\ResourceModel\SystemLog $logResource;

    public function __construct(\M2E\AmazonMcf\Model\ResourceModel\SystemLog $logResource)
    {
        $this->logResource = $logResource;
    }

    public function create(\M2E\AmazonMcf\Model\SystemLog $systemLog): void
    {
        $this->logResource->save($systemLog);
    }
}
