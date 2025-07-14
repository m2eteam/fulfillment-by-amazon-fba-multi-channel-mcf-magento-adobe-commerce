<?php

namespace M2E\AmazonMcf\Plugin;

use M2E\AmazonMcf\Model\Exception;

abstract class AbstractPlugin
{
    protected \M2E\AmazonMcf\Model\Module $module;
    private \M2E\AmazonMcf\Helper\Module\Maintenance $maintenance;
    private \M2E\AmazonMcf\Helper\Magento $magentoHelper;

    public function __construct(
        \M2E\AmazonMcf\Model\Module $module,
        \M2E\AmazonMcf\Helper\Module\Maintenance $maintenance,
        \M2E\AmazonMcf\Helper\Magento $magentoHelper
    ) {
        $this->module = $module;
        $this->maintenance = $maintenance;
        $this->magentoHelper = $magentoHelper;
    }

    /**
     * @throws \M2E\AmazonMcf\Model\Exception
     */
    protected function execute($name, $interceptor, \Closure $callback, array $arguments = [])
    {
        if (!$this->canExecute()) {
            return empty($arguments)
                ? $callback()
                : call_user_func_array($callback, $arguments);
        }

        $processMethod = 'process' . ucfirst($name);
        if (!method_exists($this, $processMethod)) {
            throw new Exception("Method $processMethod doesn't exists");
        }

        return $this->{$processMethod}($interceptor, $callback, $arguments);
    }

    protected function canExecute(): bool
    {
        if (
            $this->magentoHelper->isInstalled() === false
            || $this->maintenance->isEnabled()
            || !$this->module->areImportantTablesExist()
            || $this->module->isDisabled()
        ) {
            return false;
        }

        return true;
    }
}
