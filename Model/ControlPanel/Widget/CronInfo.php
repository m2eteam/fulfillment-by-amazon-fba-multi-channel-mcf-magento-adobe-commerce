<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\ControlPanel\Widget;

class CronInfo implements \M2E\Core\Model\ControlPanel\Widget\CronInfoInterface
{
    private \M2E\AmazonMcf\Model\Cron\Manager $cronManager;

    public function __construct(\M2E\AmazonMcf\Model\Cron\Manager $cronManager)
    {
        $this->cronManager = $cronManager;
    }

    public function isCronWorking(): bool
    {
        return !$this->cronManager->isCronLastRunMoreThan(3600);
    }

    public function getCronLastRunTime(): ?\DateTimeInterface
    {
        return $this->cronManager->getDateCronLastRun();
    }

    public function isRunnerTypeMagento(): bool
    {
        return true;
    }

    public function isRunnerTypeDeveloper(): bool
    {
        return false;
    }

    public function isRunnerTypeServiceController(): bool
    {
        return false;
    }

    public function isRunnerTypeServicePub(): bool
    {
        return false;
    }

    public function isMagentoCronDisabled(): bool
    {
        return false;
    }

    public function isControllerCronDisabled(): bool
    {
        return false;
    }

    public function isServicePubDisabled(): bool
    {
        return false;
    }

    public function getServiceAuthKey(): string
    {
        return '';
    }
}
