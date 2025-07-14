<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Cron;

abstract class AbstractRunner
{
    public const MAX_MEMORY_LIMIT = 2048;

    private \M2E\AmazonMcf\Model\Module $module;
    private \M2E\AmazonMcf\Helper\M2EPro $m2eproHelper;
    private \Magento\Store\Model\StoreManagerInterface $storeManager;
    private \M2E\AmazonMcf\Model\OperationHistory $operationHistory;
    private \M2E\AmazonMcf\Model\Lock\Transactional\ManagerFactory $lockTransactionManagerFactory;
    private \M2E\AmazonMcf\Model\SystemLog\Logger $systemLogger;
    private \M2E\AmazonMcf\Helper\Magento $magentoHelper;
    private \M2E\AmazonMcf\Model\Cron\Manager $cronManager;
    private \M2E\AmazonMcf\Model\Cron\Config $cronConfig;
    private \M2E\AmazonMcf\Helper\Module\Maintenance $maintenance;
    private \M2E\AmazonMcf\Model\OperationHistoryFactory $operationHistoryFactory;
    private \M2E\AmazonMcf\Model\Cron\Strategy $strategy;
    private \M2E\Core\Helper\Client\MemoryLimit $memoryLimit;
    private ?int $previousStoreId;

    public function __construct(
        \M2E\AmazonMcf\Model\Module $module,
        \M2E\AmazonMcf\Helper\M2EPro $m2eproHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \M2E\AmazonMcf\Model\Lock\Transactional\ManagerFactory $lockTransactionManagerFactory,
        \M2E\AmazonMcf\Model\SystemLog\Logger $systemLogger,
        \M2E\AmazonMcf\Helper\Magento $magentoHelper,
        \M2E\AmazonMcf\Model\Config\Manager $configManager,
        \M2E\AmazonMcf\Model\Cron\Manager $cronManager,
        \M2E\AmazonMcf\Model\Cron\Config $cronConfig,
        \M2E\AmazonMcf\Helper\Module\Maintenance $maintenance,
        \M2E\AmazonMcf\Model\OperationHistoryFactory $operationHistoryFactory,
        \M2E\Core\Helper\Client\MemoryLimit $memoryLimit,
        \M2E\AmazonMcf\Model\Cron\Strategy $strategy
    ) {
        $this->module = $module;
        $this->storeManager = $storeManager;
        $this->lockTransactionManagerFactory = $lockTransactionManagerFactory;
        $this->systemLogger = $systemLogger;
        $this->magentoHelper = $magentoHelper;
        $this->cronManager = $cronManager;
        $this->cronConfig = $cronConfig;
        $this->maintenance = $maintenance;
        $this->operationHistoryFactory = $operationHistoryFactory;
        $this->strategy = $strategy;
        $this->memoryLimit = $memoryLimit;
        $this->m2eproHelper = $m2eproHelper;
    }

    abstract public function getNick(): ?string;

    abstract public function getInitiator(): int;

    private function canProcess(): bool
    {
        if (
            $this->m2eproHelper->isModuleDisabled()
            || !$this->magentoHelper->isInstalled()
            || $this->maintenance->isEnabled()
            || $this->module->isDisabled()
            || ($this->getNick() !== null && $this->cronConfig->isTaskDisabled($this->getNick()))
        ) {
            return false;
        }

        return true;
    }

    public function process(): void
    {
        if (!$this->canProcess()) {
            return;
        }

        $transactionalManager = $this->lockTransactionManagerFactory->create('cron_runner');

        $transactionalManager->lock();

        $this->initialize();
        $this->setLastAccess();

        if (!$this->isPossibleToRun()) {
            $this->deInitialize();
            $transactionalManager->unlock();

            return;
        }

        $this->setLastRun();
        $this->beforeStart();

        $transactionalManager->unlock();

        try {
            $strategy = $this->getStrategy();

            $strategy->setInitiator($this->getInitiator());
            $strategy->setParentOperationHistory($this->getOperationHistory());

            $strategy->process();
        } catch (\Throwable $exception) {
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

        $this->afterEnd();
        $this->deInitialize();
    }

    protected function getStrategy(): \M2E\AmazonMcf\Model\Cron\Strategy
    {
        return $this->strategy;
    }

    private function initialize(): void
    {
        $this->previousStoreId = (int)$this->storeManager->getStore()->getId();

        $this->storeManager->setCurrentStore(\Magento\Store\Model\Store::DEFAULT_STORE_ID);

        $this->memoryLimit->set(self::MAX_MEMORY_LIMIT);
    }

    private function deInitialize(): void
    {
        if ($this->previousStoreId !== null) {
            $this->storeManager->setCurrentStore($this->previousStoreId);
            $this->previousStoreId = null;
        }
    }

    protected function setLastAccess(): void
    {
        $this->cronManager->setDateCronLastAccess();
    }

    protected function isPossibleToRun(): bool
    {
        if (
            !$this->module->isReadyToWork()
            || !$this->cronConfig->isCronEnabled()
        ) {
            return false;
        }

        return true;
    }

    protected function setLastRun(): void
    {
        $this->cronManager->setDateCronLastRun();
    }

    // ---------------------------------------

    protected function beforeStart(): void
    {
        $this->getOperationHistory()->start(
            'cron_runner',
            $this->getInitiator(),
            $this->getOperationHistoryData()
        );
        $this->getOperationHistory()->makeShutdownFunction();
    }

    protected function afterEnd(): void
    {
        $this->getOperationHistory()->stop();
    }

    // ---------------------------------------

    protected function getOperationHistoryData(): array
    {
        return ['runner' => $this->getNick()];
    }

    public function getOperationHistory(): \M2E\AmazonMcf\Model\OperationHistory
    {
        return $this->operationHistory ?? ($this->operationHistory = $this->operationHistoryFactory->create());
    }
}
