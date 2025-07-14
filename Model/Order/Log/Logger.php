<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Order\Log;

class Logger
{
    private Repository $repository;
    private \M2E\AmazonMcf\Model\Order\LogFactory $logFactory;
    private \M2E\AmazonMcf\Model\SystemLog\Logger $systemLogger;

    public function __construct(
        Repository $repository,
        \M2E\AmazonMcf\Model\Order\LogFactory $logFactory,
        \M2E\AmazonMcf\Model\SystemLog\Logger $systemLogger
    ) {
        $this->repository = $repository;
        $this->logFactory = $logFactory;
        $this->systemLogger = $systemLogger;
    }

    public function warning(
        string $message,
        int $orderId,
        int $initiator = \M2E\AmazonMcf\Model\Logger\Initiator::INITIATOR_EXTENSION,
        array $context = []
    ): void {
        $this->write(
            \M2E\AmazonMcf\Model\SystemLog\Logger::SEVERITY_WARNING,
            $message,
            $orderId,
            $initiator,
            $context
        );
    }

    public function notice(
        string $message,
        int $orderId,
        int $initiator = \M2E\AmazonMcf\Model\Logger\Initiator::INITIATOR_EXTENSION,
        array $context = []
    ): void {
        $this->write(
            \M2E\AmazonMcf\Model\SystemLog\Logger::SEVERITY_NOTICE,
            $message,
            $orderId,
            $initiator,
            $context
        );
    }

    public function error(
        string $message,
        int $orderId,
        int $initiator = \M2E\AmazonMcf\Model\Logger\Initiator::INITIATOR_EXTENSION,
        array $context = []
    ): void {
        $this->write(
            \M2E\AmazonMcf\Model\SystemLog\Logger::SEVERITY_ERROR,
            $message,
            $orderId,
            $initiator,
            $context
        );
    }

    public function info(
        string $message,
        int $orderId,
        int $initiator = \M2E\AmazonMcf\Model\Logger\Initiator::INITIATOR_EXTENSION,
        array $context = []
    ): void {
        $this->write(
            \M2E\AmazonMcf\Model\SystemLog\Logger::SEVERITY_INFO,
            $message,
            $orderId,
            $initiator,
            $context
        );
    }

    private function write(
        int $severity,
        string $message,
        int $orderId,
        int $initiator,
        array $context
    ): void {
        try {
            $this->assertInitiator($initiator);
            $log = $this->logFactory->create();
            $log->init(
                $orderId,
                $message,
                $severity,
                $initiator
            );
            if (!empty($context)) {
                $log->setContext($context);
            }

            $this->repository->create($log);
        } catch (\Throwable $e) {
            $this->systemLogger->exception($e);
        }
    }

    private function assertInitiator(int $initiator): void
    {
        if (!\M2E\AmazonMcf\Model\Logger\Initiator::isFamousInitiator($initiator)) {
            throw new \LogicException(
                sprintf('Unresolved log initiator - "%d"', $initiator)
            );
        }
    }
}
