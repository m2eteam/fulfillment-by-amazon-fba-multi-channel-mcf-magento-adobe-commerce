<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Observer\Order;

class MagentoOrderCreated implements \Magento\Framework\Event\ObserverInterface
{
    private \M2E\AmazonMcf\Model\Order\CreateService $orderCreateService;
    private \M2E\AmazonMcf\Model\Module $module;
    private \M2E\AmazonMcf\Model\SystemLog\Logger $systemLogger;
    private \M2E\AmazonMcf\Helper\M2EPro $m2eproHelper;

    public function __construct(
        \M2E\AmazonMcf\Model\Order\CreateService $orderCreateService,
        \M2E\AmazonMcf\Model\Module $moduleConfig,
        \M2E\AmazonMcf\Model\SystemLog\Logger $systemLogger,
        \M2E\AmazonMcf\Helper\M2EPro $m2eproHelper
    ) {
        $this->orderCreateService = $orderCreateService;
        $this->module = $moduleConfig;
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

        try {
            $orderInput = $this->retrieveServiceOrderInput($observer->getEvent());
            if (
                $orderInput === null
                || !$this->orderCreateService->canCreate($orderInput)
            ) {
                return;
            }

            $this->orderCreateService->create($orderInput);
        } catch (\Throwable $e) {
            $this->systemLogger->exception($e);
        }
    }

    private function retrieveServiceOrderInput(
        \Magento\Framework\Event $event
    ): ?\M2E\AmazonMcf\Model\Order\CreateService\OrderInput {
        if (!MagentoOrderCreated\EventParameters::isValid($event)) {
            return null;
        }

        $params = new MagentoOrderCreated\EventParameters($event);

        return new \M2E\AmazonMcf\Model\Order\CreateService\OrderInput(
            $params->channel,
            $params->channelOrderId,
            $params->magentoOrderId,
            $params->magentoOrderIncrementId,
            $params->region,
            $params->channelExternalOrderId,
            $params->channelPurchaseDate
        );
    }
}
