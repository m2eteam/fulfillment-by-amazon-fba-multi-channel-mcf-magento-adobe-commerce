<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Observer\Magento\Order;

class QuoteSubmitSuccess implements \Magento\Framework\Event\ObserverInterface
{
    private \M2E\AmazonMcf\Model\Module $module;
    private \M2E\AmazonMcf\Model\Order\CreateService $orderCreateService;
    private \M2E\AmazonMcf\Model\SystemLog\Logger $systemLogger;

    public function __construct(
        \M2E\AmazonMcf\Model\Module $module,
        \M2E\AmazonMcf\Model\Order\CreateService $orderCreateService,
        \M2E\AmazonMcf\Model\SystemLog\Logger $systemLogger
    ) {
        $this->module = $module;
        $this->orderCreateService = $orderCreateService;
        $this->systemLogger = $systemLogger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer): void
    {
        if ($this->module->isDisabled()) {
            return;
        }

        $event = $observer->getEvent();
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $event->getData('quote');
        /** @var \Magento\Sales\Model\Order $magentoOrder */
        $magentoOrder = $event->getData('order');

        if (
            empty($quote)
            || empty($magentoOrder)
        ) {
            return;
        }

        try {
            if (!$this->orderCreateService->canCreateForMagentoChannel($quote, $magentoOrder)) {
                return;
            }

            $this->orderCreateService->createForMagentoChannel($magentoOrder, $quote);
        } catch (\Throwable $e) {
            $this->systemLogger->exception($e);
        }
    }
}
