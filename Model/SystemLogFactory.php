<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model;

class SystemLogFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(): SystemLog
    {
        return $this->objectManager->create(SystemLog::class);
    }
}
