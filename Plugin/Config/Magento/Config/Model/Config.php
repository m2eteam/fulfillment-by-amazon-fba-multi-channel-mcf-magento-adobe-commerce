<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Plugin\Config\Magento\Config\Model;

class Config extends \M2E\AmazonMcf\Plugin\AbstractPlugin
{
    private \Magento\Framework\App\RequestInterface $request;
    private \M2E\AmazonMcf\Model\Account\ForceSyncConfig $accountForceSyncConfig;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \M2E\AmazonMcf\Model\Module $module,
        \M2E\AmazonMcf\Model\Account\ForceSyncConfig $accountForceSyncConfig,
        \M2E\AmazonMcf\Helper\Module\Maintenance $maintenance,
        \M2E\AmazonMcf\Helper\Magento $magentoHelper
    ) {
        parent::__construct($module, $maintenance, $magentoHelper);

        $this->request = $request;
        $this->accountForceSyncConfig = $accountForceSyncConfig;
    }

    /**
     * @return mixed
     * @throws \M2E\AmazonMcf\Model\Exception
     */
    public function aroundSave(\Magento\Config\Model\Config $interceptor, \Closure $callback, ...$arguments)
    {
        return $this->execute('save', $interceptor, $callback, $arguments);
    }

    /**
     * @return \Magento\Config\Model\Config|mixed
     */
    protected function processSave(\Magento\Config\Model\Config $interceptor, \Closure $callback, array $arguments)
    {
        $saveData = $this->request->getParams();

        if (!isset($saveData['section']) || $saveData['section'] !== 'amazonmcf_module') {
            return $callback(...$arguments);
        }

        $this->processModuleSection($saveData['groups']);

        return $interceptor;
    }

    private function processModuleSection(array $groups): void
    {
        if (!isset($groups['general']['fields']['is_enabled']['value'])) {
            return;
        }
        $isEnabledValue = (bool)(int)$groups['general']['fields']['is_enabled']['value'];

        if ($isEnabledValue) {
            if ($this->module->isDisabled()) {
                $this->accountForceSyncConfig->enable();
            }

            $this->module->enable();

            return;
        }

        $this->module->disable();
    }

    protected function canExecute(): bool
    {
        return true;
    }
}
