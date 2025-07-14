<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\SystemLog;

class Logger
{
    public const SEVERITY_INFO = 200;
    public const SEVERITY_NOTICE = 250;
    public const SEVERITY_WARNING = 300;
    public const SEVERITY_ERROR = 400;
    public const SEVERITY_CRITICAL = 500;

    private static array $psrLevelMap = [
        self::SEVERITY_INFO => \Psr\Log\LogLevel::INFO,
        self::SEVERITY_NOTICE => \Psr\Log\LogLevel::NOTICE,
        self::SEVERITY_WARNING => \Psr\Log\LogLevel::WARNING,
        self::SEVERITY_ERROR => \Psr\Log\LogLevel::ERROR,
        self::SEVERITY_CRITICAL => \Psr\Log\LogLevel::CRITICAL,
    ];

    private Repository $repository;
    private \M2E\AmazonMcf\Model\SystemLogFactory $systemLogFactory;
    private \Psr\Log\LoggerInterface $psrLogger;
    private Logger\ExceptionInformer $exceptionInformer;

    public function __construct(
        Repository $repository,
        \M2E\AmazonMcf\Model\SystemLogFactory $systemLogFactory,
        \Psr\Log\LoggerInterface $psrLogger,
        Logger\ExceptionInformer $exceptionInformer
    ) {
        $this->repository = $repository;
        $this->systemLogFactory = $systemLogFactory;
        $this->psrLogger = $psrLogger;
        $this->exceptionInformer = $exceptionInformer;
    }

    public function info(string $message, array $context = []): void
    {
        $this->write(self::SEVERITY_INFO, $message, $context);
    }

    public function notice(string $message, array $context = []): void
    {
        $this->write(self::SEVERITY_NOTICE, $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->write(self::SEVERITY_ERROR, $message, $context);
    }

    public function exception(\Throwable $exception, array $context = []): void
    {
        $context = array_merge(
            $context,
            ['exception_info' => $this->exceptionInformer->getExceptionInfo($exception)]
        );
        $this->write(self::SEVERITY_CRITICAL, $exception->getMessage(), $context);
    }

    private function write(int $severity, string $message, array $context): void
    {
        try {
            $log = $this->systemLogFactory->create();
            $log->init($severity, $message);

            if (!empty($context)) {
                $log->setContext($context);
            }

            $this->repository->create($log);
        } catch (\Throwable $e) {
            $this->psrLogger->log(self::$psrLevelMap[$severity], $message, $context);
            $this->psrLogger->critical(
                $e->getMessage(),
                $this->exceptionInformer->getExceptionInfo($e)
            );
        }
    }
}
