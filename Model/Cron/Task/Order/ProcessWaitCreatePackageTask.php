<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Cron\Task\Order;

use M2E\AmazonMcf\Model\Amazon\Connector\Exception\AuthorizationException;
use M2E\AmazonMcf\Model\Amazon\Connector\Exception\SystemUnavailableException;
use M2E\AmazonMcf\Model\Amazon\Connector\Exception\ThrottlingException;
use M2E\AmazonMcf\Model\Order\StatusProcessor\WaitCreatePackageStatusProcessor;

class ProcessWaitCreatePackageTask extends \M2E\AmazonMcf\Model\Cron\AbstractTask
{
    public const NICK = 'orders/process_wait_create_package_status';

    private const ORDERS_LIMIT_COUNT = 100;
    private const ATTEMPT_DATE_INTERVAL_IN_HOURS = 1;
    private const ATTEMPT_MAX_COUNT = 5;

    private \M2E\AmazonMcf\Model\Order\Log\Logger $orderLogger;
    private \M2E\AmazonMcf\Model\Order\Repository $orderRepository;
    private WaitCreatePackageStatusProcessor $waitCreatePackageStatusProcessor;

    public function __construct(
        \M2E\AmazonMcf\Model\Order\Log\Logger $orderLogger,
        \M2E\AmazonMcf\Model\Order\Repository $orderRepository,
        WaitCreatePackageStatusProcessor $waitCreatePackageStatusProcessor,
        \M2E\AmazonMcf\Model\Cron\Manager $cronManager,
        \Magento\Framework\Event\Manager $eventManager,
        \M2E\AmazonMcf\Model\SystemLog\Logger $systemLogger,
        \M2E\AmazonMcf\Model\OperationHistoryFactory $operationHistoryFactory,
        \M2E\AmazonMcf\Model\Config\Manager $configManager
    ) {
        parent::__construct(
            $cronManager,
            $eventManager,
            $systemLogger,
            $operationHistoryFactory,
            $configManager
        );

        $this->orderLogger = $orderLogger;
        $this->orderRepository = $orderRepository;
        $this->waitCreatePackageStatusProcessor = $waitCreatePackageStatusProcessor;
    }

    protected function performActions(): void
    {
        $attemptDatePoint = \M2E\Core\Helper\Date::createCurrentGmt()->modify(
            sprintf('-%d hours', self::ATTEMPT_DATE_INTERVAL_IN_HOURS)
        );
        $orders = $this->orderRepository->findWithStatusWaitCreatedPackage(
            self::ORDERS_LIMIT_COUNT,
            $attemptDatePoint
        );

        foreach ($orders as $order) {
            $order->waitNextStatusProcess();
            $this->orderRepository->save($order);

            try {
                $this->waitCreatePackageStatusProcessor->process($order);
            } catch (AuthorizationException | SystemUnavailableException | ThrottlingException $exception) {
                $this->addLogUnableToProcessRequest($order);
            } catch (\Throwable $exception) {
                $this->systemLogger->exception($exception);
            } finally {
                if ($order->isWaitCreatedPackageStatus()) {
                    if (
                        isset($exception)
                        && $order->getStatusProcessAttemptCount() >= self::ATTEMPT_MAX_COUNT
                    ) {
                        $this->skipOrder($order);

                        return;
                    } elseif (!isset($exception)) {
                        $this->clearProcessAttemptCount($order);
                    }
                }
            }
        }
    }

    private function addLogUnableToProcessRequest(\M2E\AmazonMcf\Model\Order $order): void
    {
        $this->orderLogger->warning(
            (string)__(
                'Unable to process request.'
                . ' Possible reasons: Amazon Authorization failed, the system not available,'
                . ' API throttling limit reached.'
            ),
            $order->getId()
        );
    }

    private function skipOrder(\M2E\AmazonMcf\Model\Order $order): void
    {
        $this->orderLogger->warning(
            (string)__('Order is Unprocessed due to an issue with the request.'),
            $order->getId()
        );

        $order->setStatusSkipped();
        $this->orderRepository->save($order);
    }

    private function clearProcessAttemptCount(\M2E\AmazonMcf\Model\Order $order): void
    {
        $order->clearProcessAttemptCount();
        $this->orderRepository->save($order);
    }

    protected function getNick(): string
    {
        return self::NICK;
    }
}
