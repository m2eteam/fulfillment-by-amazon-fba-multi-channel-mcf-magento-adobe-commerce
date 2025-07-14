<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Cron;

class Config
{
    private \M2E\AmazonMcf\Model\Config\Manager $config;

    public function __construct(\M2E\AmazonMcf\Model\Config\Manager $config)
    {
        $this->config = $config;
    }

    public function isCronEnabled(): bool
    {
        return (bool)(int)$this->config->getGroupValue('/cron/', 'is_enabled');
    }

    public function isTaskDisabled(string $taskNick): bool
    {
        return (bool)(int)$this->config->getGroupValue(
            '/cron/' . $taskNick . '/',
            'disabled'
        );
    }
}
