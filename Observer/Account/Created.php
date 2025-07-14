<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Observer\Account;

class Created implements \Magento\Framework\Event\ObserverInterface
{
    private const EVENT_REGION_AMERICA = 'america';
    private const EVENT_REGION_EUROPE = 'europe';
    private const EVENT_REGION_ASIA_PACIFIC = 'asia-pacific';

    private \M2E\AmazonMcf\Model\Account\CreateService $createService;
    private \M2E\AmazonMcf\Model\Module $module;
    private \M2E\AmazonMcf\Model\SystemLog\Logger $systemLogger;
    private \M2E\AmazonMcf\Helper\M2EPro $m2eproHelper;

    public function __construct(
        \M2E\AmazonMcf\Model\Account\CreateService $createService,
        \M2E\AmazonMcf\Model\Module $module,
        \M2E\AmazonMcf\Model\SystemLog\Logger $systemLogger,
        \M2E\AmazonMcf\Helper\M2EPro $m2eproHelper
    ) {
        $this->createService = $createService;
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
        $isEnabled = $event->getData('is_enabled_manage_fba_inventory');
        $merchantId = $event->getData('merchant_id');
        $region = $this->resolveRegion($event) ?? $this->getRegionByFlags($event);

        if (
            $isEnabled === null
            || $region === null
            || empty($merchantId)
        ) {
            return;
        }

        try {
            $this->createService->create($merchantId, $region, $isEnabled);
        } catch (\Throwable $e) {
            $this->systemLogger->exception($e);
        }
    }

    private function resolveRegion(\Magento\Framework\Event $event): ?string
    {
        $region = $event->getData('region');
        if ($region === null) {
            return null;
        }

        $map = [
            self::EVENT_REGION_AMERICA => \M2E\AmazonMcf\Model\Account::REGION_AMERICA,
            self::EVENT_REGION_EUROPE => \M2E\AmazonMcf\Model\Account::REGION_EUROPE,
            self::EVENT_REGION_ASIA_PACIFIC => \M2E\AmazonMcf\Model\Account::REGION_ASIA_PACIFIC,
        ];

        return $map[$region] ?? null;
    }

    /**
     * Region flags are necessary to maintain backward compatibility. Give priority to resolveRegion()
     */
    private function getRegionByFlags(\Magento\Framework\Event $event): ?string
    {
        if ($event->getData('is_american_region')) {
            return \M2E\AmazonMcf\Model\Account::REGION_AMERICA;
        }

        if ($event->getData('is_european_region')) {
            return \M2E\AmazonMcf\Model\Account::REGION_EUROPE;
        }

        if ($event->getData('is_asian_pacific_region')) {
            return \M2E\AmazonMcf\Model\Account::REGION_ASIA_PACIFIC;
        }

        return null;
    }
}
