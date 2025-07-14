<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Observer\Product;

class Updated implements \Magento\Framework\Event\ObserverInterface
{
    private \M2E\AmazonMcf\Model\Account\Repository $accountRepository;
    private \M2E\AmazonMcf\Model\Module $module;
    private \M2E\AmazonMcf\Model\SystemLog\Logger $systemLogger;
    private \M2E\AmazonMcf\Model\Product\SynchronizeService $productSynchronizeService;
    private \M2E\AmazonMcf\Helper\M2EPro $m2eproHelper;

    public function __construct(
        \M2E\AmazonMcf\Model\Account\Repository $accountRepository,
        \M2E\AmazonMcf\Model\Module $moduleConfig,
        \M2E\AmazonMcf\Model\Product\SynchronizeService $productSynchronizeService,
        \M2E\AmazonMcf\Model\SystemLog\Logger $systemLogger,
        \M2E\AmazonMcf\Helper\M2EPro $m2eproHelper
    ) {
        $this->accountRepository = $accountRepository;
        $this->module = $moduleConfig;
        $this->systemLogger = $systemLogger;
        $this->productSynchronizeService = $productSynchronizeService;
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

        $merchantId = $observer->getEvent()->getData('merchant_id');
        if (empty($merchantId)) {
            return;
        }

        try {
            if ($account = $this->accountRepository->findByMerchantId($merchantId)) {
                $this->productSynchronizeService->sync($account);
            }
        } catch (\Throwable $e) {
            $this->systemLogger->exception($e);
        }
    }
}
