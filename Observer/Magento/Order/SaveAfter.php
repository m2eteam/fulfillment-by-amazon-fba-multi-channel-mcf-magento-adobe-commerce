<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Observer\Magento\Order;

class SaveAfter implements \Magento\Framework\Event\ObserverInterface
{
    private \M2E\AmazonMcf\Model\Module $module;
    private \M2E\AmazonMcf\Model\Order\PayService $payService;
    private \M2E\AmazonMcf\Model\Order\Repository $orderRepository;
    private \M2E\AmazonMcf\Model\SystemLog\Logger $systemLogger;

    public function __construct(
        \M2E\AmazonMcf\Model\Module $module,
        \M2E\AmazonMcf\Model\Order\Repository $orderRepository,
        \M2E\AmazonMcf\Model\Order\PayService $payService,
        \M2E\AmazonMcf\Model\SystemLog\Logger $systemLogger
    ) {
        $this->payService = $payService;
        $this->orderRepository = $orderRepository;
        $this->module = $module;
        $this->systemLogger = $systemLogger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer): void
    {
        if ($this->module->isDisabled()) {
            return;
        }

        /** @var \Magento\Sales\Api\Data\OrderInterface $order */
        $order = $observer->getEvent()->getOrder();
        $orderId = (int)$order->getEntityId();

        if (empty($orderId)) {
            return;
        }

        try {
            $mcfOrder = $this->orderRepository->findByMagentoOrderId(
                $orderId
            );

            if (
                $mcfOrder === null
                || !$this->payService->canPay($mcfOrder)
                || $order->getState() !== \Magento\Sales\Model\Order::STATE_PROCESSING
            ) {
                return;
            }

            $this->payService->pay($mcfOrder);
        } catch (\Throwable $e) {
            $this->systemLogger->exception($e);
        }
    }
}
