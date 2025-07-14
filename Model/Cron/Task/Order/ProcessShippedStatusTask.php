<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Cron\Task\Order;

class ProcessShippedStatusTask extends \M2E\AmazonMcf\Model\Cron\AbstractTask
{
    public const NICK = 'orders/process_shipped_status';

    private const ORDERS_LIMIT_COUNT = 100;
    private const ATTEMPT_DATE_INTERVAL_IN_HOURS = 1;
    private const ATTEMPT_MAX_COUNT = 5;

    private \M2E\AmazonMcf\Model\Order\Repository $orderRepository;
    private \M2E\AmazonMcf\Model\Order\StatusProcessor\ShippedStatusProcessor $shippedStatusProcessor;
    private \M2E\AmazonMcf\Model\Order\Log\Logger $orderLogger;

    public function __construct(
        \M2E\AmazonMcf\Model\Order\Repository $orderRepository,
        \M2E\AmazonMcf\Model\Order\Log\Logger $orderLogger,
        \M2E\AmazonMcf\Model\Order\StatusProcessor\ShippedStatusProcessor $shippedStatusProcessor,
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

        $this->orderRepository = $orderRepository;
        $this->shippedStatusProcessor = $shippedStatusProcessor;
        $this->orderLogger = $orderLogger;
    }

    protected function performActions(): void
    {
        $attemptDatePoint = \M2E\Core\Helper\Date::createCurrentGmt()->modify(
            sprintf('-%d hours', self::ATTEMPT_DATE_INTERVAL_IN_HOURS)
        );
        $orders = $this->orderRepository->findWithStatusShipped(self::ORDERS_LIMIT_COUNT, $attemptDatePoint);
        foreach ($orders as $order) {
            $order->waitNextStatusProcess();
            $this->orderRepository->save($order);

            try {
                $this->shippedStatusProcessor->process($order);
            } catch (\Throwable $exception) {
                $this->systemLogger->exception($exception);

                if (
                    $order->isStatusShipped()
                    && $order->getStatusProcessAttemptCount() >= self::ATTEMPT_MAX_COUNT
                ) {
                    $this->skipOrder($order);
                }
            }
        }
    }

    private function skipOrder(\M2E\AmazonMcf\Model\Order $order): void
    {
        $this->orderLogger->warning(
            (string)__('Order is Unprocessed because there was a problem creating the shipment in Magento.'),
            $order->getId()
        );
        $order->setStatusSkipped();
        $this->orderRepository->save($order);
    }

    protected function getNick(): string
    {
        return self::NICK;
    }
}
