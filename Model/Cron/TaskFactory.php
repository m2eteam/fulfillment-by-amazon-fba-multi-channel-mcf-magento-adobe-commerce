<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Cron;

class TaskFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function createByClassName(
        string $className,
        int $initiator,
        \M2E\AmazonMcf\Model\OperationHistory $operationHistory,
        \M2E\AmazonMcf\Model\Lock\Item\Manager $lockItemManager
    ): AbstractTask {
        /** @var AbstractTask $task */
        $task = $this->objectManager->create($className);

        if (!$task instanceof AbstractTask) {
            throw new \M2E\AmazonMcf\Model\Exception\Logic('Invalid instance');
        }

        $task->init($initiator, $operationHistory, $lockItemManager);

        return $task;
    }
}
