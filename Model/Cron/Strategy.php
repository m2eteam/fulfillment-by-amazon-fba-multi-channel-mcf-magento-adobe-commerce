<?php

namespace M2E\AmazonMcf\Model\Cron;

class Strategy
{
    public const LOCK_ITEM_NICK = 'cron_strategy_serial';
    public const INITIALIZATION_TRANSACTIONAL_LOCK_NICK = 'cron_strategy_initialization';
    public const PROGRESS_START_EVENT_NAME = 'm2e_mcf_cron_progress_start';
    public const PROGRESS_SET_PERCENTAGE_EVENT_NAME = 'm2e_mcf_cron_progress_set_percentage';
    public const PROGRESS_SET_DETAILS_EVENT_NAME = 'm2e_mcf_cron_progress_set_details';
    public const PROGRESS_STOP_EVENT_NAME = 'm2e_mcf_cron_progress_stop';

    private TaskFactory $taskFactory;
    private \M2E\AmazonMcf\Model\Cron\Strategy\Observer\KeepAlive $observerKeepAlive;
    private \M2E\AmazonMcf\Model\Cron\Strategy\Observer\Progress $observerProgress;
    private \M2E\AmazonMcf\Model\Cron\TaskRepository $taskRepository;
    private \M2E\AmazonMcf\Model\Lock\Item\ManagerFactory $lockItemManagerFactory;
    private \M2E\AmazonMcf\Model\Lock\Item\Repository $lockItemRepository;
    private \M2E\AmazonMcf\Model\Lock\Transactional\ManagerFactory $lockTransactionalManagerFactory;
    private \M2E\AmazonMcf\Model\OperationHistoryFactory $operationHistoryFactory;
    private \M2E\AmazonMcf\Model\SystemLog\Logger $systemLogger;
    private ?\M2E\AmazonMcf\Model\Lock\Item\Manager $lockItemManager = null;
    private ?\M2E\AmazonMcf\Model\Lock\Transactional\Manager $initializationLockManager = null;
    private ?int $initiator = null;
    private ?\M2E\AmazonMcf\Model\OperationHistory $operationHistory = null;
    private ?\M2E\AmazonMcf\Model\OperationHistory $parentOperationHistory = null;
    private ?array $allowedTasks = null;

    public function __construct(
        TaskFactory $taskFactory,
        TaskRepository $taskRepository,
        \M2E\AmazonMcf\Model\Lock\Transactional\ManagerFactory $lockTransactionalManagerFactory,
        \M2E\AmazonMcf\Model\Lock\Item\Repository $lockItemRepository,
        \M2E\AmazonMcf\Model\SystemLog\Logger $systemLogger,
        \M2E\AmazonMcf\Model\Lock\Item\ManagerFactory $lockItemManagerFactory,
        Strategy\Observer\KeepAlive $observerKeepAlive,
        Strategy\Observer\Progress $observerProgress,
        \M2E\AmazonMcf\Model\OperationHistoryFactory $operationHistoryFactory
    ) {
        $this->taskFactory = $taskFactory;
        $this->lockItemManagerFactory = $lockItemManagerFactory;
        $this->observerKeepAlive = $observerKeepAlive;
        $this->observerProgress = $observerProgress;
        $this->taskRepository = $taskRepository;
        $this->lockTransactionalManagerFactory = $lockTransactionalManagerFactory;
        $this->operationHistoryFactory = $operationHistoryFactory;
        $this->lockItemRepository = $lockItemRepository;
        $this->systemLogger = $systemLogger;
    }

    // ----------------------------------------

    public function setAllowedTasks(array $tasks): self
    {
        $this->allowedTasks = $tasks;

        return $this;
    }

    public function setInitiator(int $initiator): self
    {
        $this->initiator = $initiator;

        return $this;
    }

    public function setParentOperationHistory(\M2E\AmazonMcf\Model\OperationHistory $operationHistory): self
    {
        $this->parentOperationHistory = $operationHistory;

        return $this;
    }

    // ----------------------------------------

    private function beforeStart(): void
    {
        $parentId = $this->getParentOperationHistory()
            ? (int)$this->getParentOperationHistory()->getObject()->getId()
            : null;

        $this->getOperationHistory()->start('cron_strategy_serial', $this->getInitiator(), [], $parentId);
        $this->getOperationHistory()->makeShutdownFunction();
    }

    public function process(): void
    {
        $this->beforeStart();

        try {
            $this->processTasks();
        } catch (\Throwable $exception) {
            $this->processException($exception);
        }

        $this->afterEnd();
    }

    private function afterEnd(): void
    {
        $this->getOperationHistory()->stop();
    }

    private function processTasks(): void
    {
        if ($this->getLockItemManager() === null) {
            return;
        }

        $this->getInitializationLockManager()->lock();

        try {
            $this->getLockItemManager()->create();

            $this->makeLockItemShutdownFunction($this->getLockItemManager());

            $this->getInitializationLockManager()->unlock();

            $this->keepAliveStart($this->getLockItemManager());
            $this->startListenProgressEvents($this->getLockItemManager());

            $this->processAllTasks();

            $this->keepAliveStop();
            $this->stopListenProgressEvents();
        } catch (\Throwable $exception) {
            $this->processException($exception);
        }

        $this->getLockItemManager()->remove();
    }

    private function processAllTasks(): void
    {
        $tasks = $this->allowedTasks ?? $this->taskRepository->getClassNamesOfTasks();
        foreach ($tasks as $taskClassName) {
            try {
                $task = $this->taskFactory->createByClassName(
                    $taskClassName,
                    $this->getInitiator(),
                    $this->getOperationHistory(),
                    $this->getLockItemManager()
                );

                $task->process();
            } catch (\Throwable $exception) {
                $this->processException($exception);
            }
        }
    }

    private function getLockItemManager(): ?\M2E\AmazonMcf\Model\Lock\Item\Manager
    {
        if ($this->lockItemManager !== null) {
            return $this->lockItemManager;
        }

        $lockItemManager = $this->lockItemManagerFactory->create(self::LOCK_ITEM_NICK);
        if (!$lockItemManager->isExist()) {
            return $this->lockItemManager = $lockItemManager;
        }

        if (
            $lockItemManager->isInactiveMoreThanSeconds(
                \M2E\AmazonMcf\Model\Lock\Item\Manager::DEFAULT_MAX_INACTIVE_TIME
            )
        ) {
            $lockItemManager->remove();

            return $this->lockItemManager = $lockItemManager;
        }

        return null;
    }

    private function getInitiator(): ?int
    {
        return $this->initiator;
    }

    // ---------------------------------------

    private function getParentOperationHistory(): ?\M2E\AmazonMcf\Model\OperationHistory
    {
        return $this->parentOperationHistory;
    }

    // ---------------------------------------

    private function keepAliveStart(\M2E\AmazonMcf\Model\Lock\Item\Manager $lockItemManager): void
    {
        $this->observerKeepAlive->enable();
        $this->observerKeepAlive->setLockItemManager($lockItemManager);
    }

    private function keepAliveStop(): void
    {
        $this->observerKeepAlive->disable();
    }

    private function startListenProgressEvents(\M2E\AmazonMcf\Model\Lock\Item\Manager $lockItemManager): void
    {
        $this->observerProgress->enable();
        $this->observerProgress->setLockItemManager($lockItemManager);
    }

    private function stopListenProgressEvents(): void
    {
        $this->observerProgress->disable();
    }

    private function getOperationHistory(): \M2E\AmazonMcf\Model\OperationHistory
    {
        if ($this->operationHistory !== null) {
            return $this->operationHistory;
        }

        return $this->operationHistory = $this->operationHistoryFactory->create();
    }

    private function makeLockItemShutdownFunction(\M2E\AmazonMcf\Model\Lock\Item\Manager $lockItemManager): void
    {
        $lockItem = $this->lockItemRepository->findByNick(
            $lockItemManager->getNick()
        );
        if (!$lockItem->getId()) {
            return;
        }

        $id = $lockItem->getId();

        // @codingStandardsIgnoreLine
        register_shutdown_function(
            function () use ($id) {
                $error = error_get_last();
                if (
                    $error === null || !in_array((int)$error['type'], [
                        E_ERROR,
                        E_CORE_ERROR,
                        E_COMPILE_ERROR,
                    ])
                ) {
                    return;
                }

                $lockItem = $this->lockItemRepository->find($id);
                if ($lockItem->getId()) {
                    $lockItem->delete();
                }
            }
        );
    }

    private function getInitializationLockManager(): \M2E\AmazonMcf\Model\Lock\Transactional\Manager
    {
        if ($this->initializationLockManager !== null) {
            return $this->initializationLockManager;
        }

        $lockTransactionalManager = $this->lockTransactionalManagerFactory->create(
            self::INITIALIZATION_TRANSACTIONAL_LOCK_NICK,
        );

        return $this->initializationLockManager = $lockTransactionalManager;
    }

    private function processException(\Throwable $exception): void
    {
        $this->getOperationHistory()->addContentData(
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
}
