<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Observer\Account;

class Deleted implements \Magento\Framework\Event\ObserverInterface
{
    private \M2E\AmazonMcf\Model\Account\Repository $accountRepository;
    private \M2E\AmazonMcf\Model\Module $module;
    private \M2E\AmazonMcf\Model\Account\RemoveService $removeService;
    private \M2E\AmazonMcf\Model\SystemLog\Logger $systemLogger;
    private \M2E\AmazonMcf\Helper\M2EPro $m2eproHelper;

    public function __construct(
        \M2E\AmazonMcf\Model\Account\Repository $accountRepository,
        \M2E\AmazonMcf\Model\Module $moduleConfig,
        \M2E\AmazonMcf\Model\Account\RemoveService $removeService,
        \M2E\AmazonMcf\Model\SystemLog\Logger $systemLogger,
        \M2E\AmazonMcf\Helper\M2EPro $m2eproHelper
    ) {
        $this->accountRepository = $accountRepository;
        $this->module = $moduleConfig;
        $this->removeService = $removeService;
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
        $merchantId = $event->getData('merchant_id');

        if (empty($merchantId)) {
            return;
        }

        try {
            $account = $this->accountRepository->findByMerchantId($merchantId);
            if ($account === null) {
                return;
            }
            $this->removeService->remove($account);
        } catch (\Throwable $e) {
            $this->systemLogger->exception($e);
        }
    }
}
