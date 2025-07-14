<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\ControlPanel;

class CronTaskProvider implements \M2E\Core\Model\ControlPanel\Cron\TaskProviderInterface
{
    private const GROUP_CHANNEL = 'channel';

    /** @var \M2E\Core\Model\ControlPanel\CronTask[] */
    private array $tasks;

    public function getExtensionModuleName(): string
    {
        return Extension::NAME;
    }

    public function getTasks(): array
    {
        if (isset($this->tasks)) {
            return $this->tasks;
        }

        $tasks = [];
        foreach (\M2E\AmazonMcf\Model\Cron\TaskRepository::REGISTERED_TASKS as $nick => $code) {
            $tasks[] = new \M2E\Core\Model\ControlPanel\CronTask(
                self::GROUP_CHANNEL,
                $nick,
                $code,
            );
        }

        return $this->tasks = $tasks;
    }
}
