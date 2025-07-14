<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Cron;

class TaskRepository
{
    public const REGISTERED_TASKS = [
        Task\Order\ProcessPendingStatusTask::NICK => Task\Order\ProcessPendingStatusTask::class,
        Task\Order\ProcessWaitCreatePackageTask::NICK => Task\Order\ProcessWaitCreatePackageTask::class,
        Task\Order\ProcessWaitShipStatusTask::NICK => Task\Order\ProcessWaitShipStatusTask::class,
        Task\Order\ProcessShippedStatusTask::NICK => Task\Order\ProcessShippedStatusTask::class,
    ];

    public function getClassNamesOfTasks(): array
    {
        return array_values(self::REGISTERED_TASKS);
    }

    public function getNick(string $className): string
    {
        return array_flip(self::REGISTERED_TASKS)[$className];
    }
}
