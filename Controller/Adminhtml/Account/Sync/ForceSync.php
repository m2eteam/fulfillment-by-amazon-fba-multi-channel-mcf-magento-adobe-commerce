<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Controller\Adminhtml\Account\Sync;

class ForceSync extends \M2E\AmazonMcf\Controller\Adminhtml\AbstractBase
{
    private \M2E\AmazonMcf\Model\Account\ForceSyncConfig $accountForceSyncConfig;
    private \M2E\AmazonMcf\Model\Account\SynchronizeService $accountSynchronizeService;
    private \M2E\AmazonMcf\Model\SystemLog\Logger $systemLogger;
    private \M2E\AmazonMcf\Helper\M2EPro $m2eproHelper;

    public function __construct(
        \M2E\AmazonMcf\Model\Account\ForceSyncConfig $accountForceSyncConfig,
        \M2E\AmazonMcf\Model\Account\SynchronizeService $accountSynchronizeService,
        \M2E\AmazonMcf\Model\SystemLog\Logger $systemLogger,
        \M2E\AmazonMcf\Controller\Adminhtml\Context $context,
        \M2E\AmazonMcf\Helper\M2EPro $m2eproHelper
    ) {
        parent::__construct($context);

        $this->accountForceSyncConfig = $accountForceSyncConfig;
        $this->accountSynchronizeService = $accountSynchronizeService;
        $this->systemLogger = $systemLogger;
        $this->m2eproHelper = $m2eproHelper;
    }

    public function execute()
    {
        if (
            $this->m2eproHelper->isModuleDisabled()
            || $this->accountForceSyncConfig->isDisabled()
        ) {
            return $this->getResult();
        }

        try {
            $this->accountSynchronizeService->process();
        } catch (\Throwable $e) {
            $this->getMessageManager()->addErrorMessage(
                (string)__(
                    'Unable to synchronize your Amazon account data. Please contact our Support Team for assistance.'
                )
            );
            $this->systemLogger->exception($e);
        } finally {
            $this->accountForceSyncConfig->disable();
        }

        return $this->getResult();
    }
}
