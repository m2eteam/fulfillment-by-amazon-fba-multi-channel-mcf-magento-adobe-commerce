<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\ControlPanel;

class Extension implements \M2E\Core\Model\ControlPanel\ExtensionInterface
{
    public const NAME = 'm2e_amazonmcf';

    private \M2E\AmazonMcf\Model\Module $module;

    public function __construct(\M2E\AmazonMcf\Model\Module $module)
    {
        $this->module = $module;
    }

    public function getIdentifier(): string
    {
        return \M2E\AmazonMcf\Helper\Module::IDENTIFIER;
    }

    public function getModule(): \M2E\Core\Model\ModuleInterface
    {
        return $this->module;
    }

    public function getModuleName(): string
    {
        return self::NAME;
    }

    public function getModuleTitle(): string
    {
        return 'M2E Amazon MCF';
    }
}
