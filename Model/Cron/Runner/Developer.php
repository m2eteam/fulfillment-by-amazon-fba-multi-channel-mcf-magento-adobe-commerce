<?php

namespace M2E\AmazonMcf\Model\Cron\Runner;

use M2E\AmazonMcf\Model\Cron\AbstractRunner;

class Developer extends AbstractRunner
{
    private \M2E\AmazonMcf\Model\Cron\TaskRepository $taskRepository;
    private array $allowedTasks;

    public function __construct(
        \M2E\AmazonMcf\Model\Cron\TaskRepository $taskRepository,
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
        parent::__construct(
            $module,
            $m2eproHelper,
            $storeManager,
            $lockTransactionManagerFactory,
            $systemLogger,
            $magentoHelper,
            $configManager,
            $cronManager,
            $cronConfig,
            $maintenance,
            $operationHistoryFactory,
            $memoryLimit,
            $strategy
        );

        $this->taskRepository = $taskRepository;
    }

    public function getNick(): ?string
    {
        return null;
    }

    public function getInitiator(): int
    {
        return \M2E\AmazonMcf\Model\Cron\AbstractTask::INITIATOR_DEVELOPER;
    }

    public function process(): void
    {
        // @codingStandardsIgnoreLine
        session_write_close();
        parent::process();
    }

    protected function getStrategy(): \M2E\AmazonMcf\Model\Cron\Strategy
    {
        if (!isset($this->allowedTasks)) {
            $this->allowedTasks = $this->taskRepository->getClassNamesOfTasks();
        }

        $strategy = parent::getStrategy();
        $strategy->setAllowedTasks($this->allowedTasks);

        return $strategy;
    }

    public function setAllowedTasks(array $tasks): self
    {
        $this->allowedTasks = $tasks;

        return $this;
    }

    protected function isPossibleToRun(): bool
    {
        return true;
    }

    protected function setLastRun(): void
    {
    }

    protected function setLastAccess(): void
    {
    }
}
