<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Observer\Product;

class Deleted implements \Magento\Framework\Event\ObserverInterface
{
    private \M2E\AmazonMcf\Model\Account\Repository $accountRepository;
    private \M2E\AmazonMcf\Model\Product\Repository $productRepository;
    private \M2E\AmazonMcf\Model\Module $module;
    private \M2E\AmazonMcf\Model\SystemLog\Logger $systemLogger;
    private \M2E\AmazonMcf\Model\Product\RemoveService $removeService;
    private \M2E\AmazonMcf\Helper\M2EPro $m2eproHelper;

    public function __construct(
        \M2E\AmazonMcf\Model\Account\Repository $accountRepository,
        \M2E\AmazonMcf\Model\Product\Repository $productRepository,
        \M2E\AmazonMcf\Model\Product\RemoveService $removeService,
        \M2E\AmazonMcf\Model\Module $module,
        \M2E\AmazonMcf\Model\SystemLog\Logger $systemLogger,
        \M2E\AmazonMcf\Helper\M2EPro $m2eproHelper
    ) {
        $this->accountRepository = $accountRepository;
        $this->productRepository = $productRepository;
        $this->module = $module;
        $this->systemLogger = $systemLogger;
        $this->removeService = $removeService;
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
        $channelSku = $event->getData('channel_sku');

        if (
            empty($merchantId)
            || empty($channelSku)
        ) {
            return;
        }

        try {
            $account = $this->accountRepository->findByMerchantId($merchantId);
            if ($account === null) {
                return;
            }
            $product = $this->productRepository->findByChannelSku($channelSku, $account->getId());
            if ($product === null) {
                return;
            }

            $this->removeService->remove($product);
        } catch (\Throwable $e) {
            $this->systemLogger->exception($e);
        }
    }
}
