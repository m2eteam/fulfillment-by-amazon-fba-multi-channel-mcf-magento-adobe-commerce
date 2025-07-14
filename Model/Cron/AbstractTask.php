<?php

namespace M2E\AmazonMcf\Model\Cron;

use M2E\AmazonMcf\Model\OperationHistory;

abstract class AbstractTask
{
    public const INITIATOR_UNKNOWN = 0;
    public const INITIATOR_DEVELOPER = 2;

    protected int $initiator = self::INITIATOR_UNKNOWN;
    protected int $intervalInSeconds = 60;

    private OperationHistory $operationHistory;
    private OperationHistory $parentOperationHistory;
    protected \Magento\Framework\Event\Manager $eventManager;
    protected \M2E\AmazonMcf\Model\Lock\Item\Manager $lockItemManager;
    private \M2E\AmazonMcf\Model\Config\Manager $configManager;
    private \M2E\AmazonMcf\Model\Cron\Manager $cronManager;
    protected \M2E\AmazonMcf\Model\SystemLog\Logger $systemLogger;

    public function __construct(
        \M2E\AmazonMcf\Model\Cron\Manager $cronManager,
        \Magento\Framework\Event\Manager $eventManager,
        \M2E\AmazonMcf\Model\SystemLog\Logger $systemLogger,
        \M2E\AmazonMcf\Model\OperationHistoryFactory $operationHistoryFactory,
        \M2E\AmazonMcf\Model\Config\Manager $configManager
    ) {
        $this->operationHistory = $operationHistoryFactory->create();
        $this->cronManager = $cronManager;
        $this->eventManager = $eventManager;
        $this->configManager = $configManager;
        $this->systemLogger = $systemLogger;
    }

    public function init(
        int $initiator,
        OperationHistory $parentOperationHistory,
        \M2E\AmazonMcf\Model\Lock\Item\Manager $lockItemManager
    ) {
        $this->initiator = $initiator;
        $this->parentOperationHistory = $parentOperationHistory;
        $this->lockItemManager = $lockItemManager;
    }

    public function process(): void
    {
        $this->cronManager->setDateTaskLastAccess(
            $this->getNick()
        );

        if (!$this->isPossibleToRun()) {
            return;
        }

        $this->cronManager->setDateTaskLastRun(
            $this->getNick()
        );

        $this->beforeStart();

        try {
            $this->eventManager->dispatch(
                \M2E\AmazonMcf\Model\Cron\Strategy::PROGRESS_START_EVENT_NAME,
                ['progress_nick' => $this->getNick()]
            );

            $this->performActions();

            $this->eventManager->dispatch(
                \M2E\AmazonMcf\Model\Cron\Strategy::PROGRESS_STOP_EVENT_NAME,
                ['progress_nick' => $this->getNick()]
            );
        } catch (\Throwable $exception) {
            $this->operationHistory->addContentData(
                'exceptions',
                [
                    'message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'trace' => $exception->getTraceAsString(),
                ]
            );

            $this->systemLogger->exception($exception);
        }

        $this->afterEnd();
    }

    // ---------------------------------------

    abstract protected function performActions();

    abstract protected function getNick(): string;

    // ---------------------------------------

    public function getInitiator(): int
    {
        return $this->initiator;
    }

    /**
     * @return \M2E\AmazonMcf\Model\Lock\Item\Manager
     */
    public function getLockItemManager()
    {
        return $this->lockItemManager;
    }

    // ---------------------------------------

    public function isPossibleToRun(): bool
    {
        if ($this->getInitiator() === self::INITIATOR_DEVELOPER) {
            return true;
        }

        if (!$this->isModeEnabled()) {
            return false;
        }

        $currentTimeStamp = \M2E\Core\Helper\Date::createCurrentGmt()->getTimestamp();

        $startFrom = $this->getConfigValue('start_from');
        $startFrom = !empty($startFrom) ?
            (int)\M2E\Core\Helper\Date::createDateGmt($startFrom)->format('U')
            : $currentTimeStamp;

        return $startFrom <= $currentTimeStamp && $this->isIntervalExceeded();
    }

    // ---------------------------------------

    protected function beforeStart(): void
    {
        $parentId = $this->parentOperationHistory->getObject()->getId();
        $nick = str_replace("/", "_", $this->getNick());
        $this->operationHistory->start('cron_task_' . $nick, $this->getInitiator(), [], (int)$parentId);
        $this->operationHistory->makeShutdownFunction();
    }

    protected function afterEnd(): void
    {
        $this->operationHistory->stop();
    }

    // ---------------------------------------

    protected function isModeEnabled(): bool
    {
        $mode = $this->getConfigValue('mode');

        if ($mode !== null) {
            return (bool)$mode;
        }

        return true;
    }

    protected function isIntervalExceeded(): bool
    {
        $lastRun = $this->cronManager->getDateTaskLastRun($this->getNick());
        if ($lastRun === null) {
            return true;
        }

        $currentTimeStamp = \M2E\Core\Helper\Date::createCurrentGmt()->getTimestamp();
        $lastRunTimestamp = (int)$lastRun->format('U');

        return $currentTimeStamp > $lastRunTimestamp + $this->getIntervalInSeconds();
    }

    public function getIntervalInSeconds()
    {
        $interval = $this->getConfigValue('interval');

        return $interval === null ? $this->intervalInSeconds : (int)$interval;
    }

    protected function processTaskAccountException($message, $file, $line, $trace = null)
    {
        $this->operationHistory->addContentData(
            'exceptions',
            [
                'message' => $message,
                'file' => $file,
                'line' => $line,
                'trace' => $trace,
            ]
        );
    }

    // ---------------------------------------

    private function getConfigValue($key)
    {
        return $this->configManager->getGroupValue(
            '/cron/task/' . $this->getNick() . '/',
            $key
        );
    }
}
