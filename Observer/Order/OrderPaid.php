<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Observer\Order;

class OrderPaid implements \Magento\Framework\Event\ObserverInterface
{
    private \M2E\AmazonMcf\Model\Order\Repository $orderRepository;
    private \M2E\AmazonMcf\Model\Order\PayService $payService;
    private \M2E\AmazonMcf\Model\Module $module;
    private \M2E\AmazonMcf\Model\SystemLog\Logger $systemLogger;
    private \M2E\AmazonMcf\Helper\M2EPro $m2eproHelper;

    public function __construct(
        \M2E\AmazonMcf\Model\Order\Repository $orderRepository,
        \M2E\AmazonMcf\Model\Order\PayService $payService,
        \M2E\AmazonMcf\Model\Module $module,
        \M2E\AmazonMcf\Model\SystemLog\Logger $systemLogger,
        \M2E\AmazonMcf\Helper\M2EPro $m2eproHelper
    ) {
        $this->orderRepository = $orderRepository;
        $this->payService = $payService;
        $this->module = $module;
        $this->systemLogger = $systemLogger;
        $this->m2eproHelper = $m2eproHelper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer): void
    {
        if (
            $this->module->isDisabled()
            || $this->m2eproHelper->isModuleDisabled()
        ) {
            return;
        }

        $event = $observer->getEvent();
        $channelOrderId = $event->getData('channel_order_id');
        $channel = $event->getData('channel');
        if (
            empty($channelOrderId)
            || empty($channel)
        ) {
            return;
        }

        try {
            $mcfOrder = $this->orderRepository->findByChannelId($channelOrderId, $channel);
            if (
                $mcfOrder === null
                || !$this->payService->canPay($mcfOrder)
            ) {
                return;
            }

            $this->payService->pay($mcfOrder);
        } catch (\Throwable $e) {
            $this->systemLogger->exception($e);
        }
    }
}
