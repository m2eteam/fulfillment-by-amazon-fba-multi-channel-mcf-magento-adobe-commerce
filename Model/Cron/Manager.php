<?php

namespace M2E\AmazonMcf\Model\Cron;

class Manager
{
    private \M2E\AmazonMcf\Model\Registry\Manager $registryManager;

    public function __construct(\M2E\AmazonMcf\Model\Registry\Manager $registryManager)
    {
        $this->registryManager = $registryManager;
    }

    public function isCronLastRunMoreThan(int $interval): bool
    {
        $lastRun = $this->getDateCronLastRun();
        if ($lastRun === null) {
            return false;
        }

        $lastRunTimestamp = $lastRun->getTimestamp();

        return \M2E\Core\Helper\Date::createCurrentGmt()->getTimestamp() > $lastRunTimestamp + $interval;
    }

    public function getDateCronLastRun(): ?\DateTime
    {
        return $this->getDate('/cron/last_run/');
    }

    public function setDateCronLastRun(): void
    {
        $this->setDate('/cron/last_run/');
    }

    public function setDateCronLastAccess(): void
    {
        $this->setDate('/cron/last_access/');
    }

    // -----------------------------------------------

    public function getDateTaskLastRun(string $taskNick): ?\DateTime
    {
        return $this->getDate(
            '/cron/task/' . $taskNick . '/last_run/'
        );
    }

    public function setDateTaskLastRun(string $taskNick): void
    {
        $this->setDate(
            '/cron/task/' . $taskNick . '/last_run/'
        );
    }

    public function setDateTaskLastAccess(string $taskNick): void
    {
        $this->setDate(
            '/cron/task/' . $taskNick . '/last_access/'
        );
    }

    // -----------------------------------------------

    private function getDate(string $key): ?\DateTime
    {
        $value = $this->registryManager->getValue($key);
        if ($value === null) {
            return null;
        }

        return \M2E\Core\Helper\Date::createDateGmt($value);
    }

    private function setDate(string $key): void
    {
        $this->registryManager->setValue(
            $key,
            \M2E\Core\Helper\Date::createCurrentGmt()->format('Y-m-d H:i:s')
        );
    }
}
