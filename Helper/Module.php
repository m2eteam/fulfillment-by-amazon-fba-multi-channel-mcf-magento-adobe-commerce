<?php

namespace M2E\AmazonMcf\Helper;

class Module
{
    public const IDENTIFIER = 'M2E_AmazonMcf';
    public const MAGENTO_CONFIG_KEY_PREFIX = 'm2e_amcf/';

    public const MESSAGE_TYPE_NOTICE = 0;
    public const MESSAGE_TYPE_ERROR = 1;
    public const MESSAGE_TYPE_WARNING = 2;
    public const MESSAGE_TYPE_SUCCESS = 3;

    public const ENVIRONMENT_PRODUCTION = 'production';
    public const ENVIRONMENT_DEVELOPMENT = 'development';

    private \M2E\AmazonMcf\Model\Module $module;
    private \M2E\AmazonMcf\Model\Module\Environment $moduleEnv;

    public function __construct(
        \M2E\AmazonMcf\Model\Module $module,
        \M2E\AmazonMcf\Model\Module\Environment $moduleEnv
    ) {
        $this->module = $module;
        $this->moduleEnv = $moduleEnv;
    }

    public function getPublicVersion(): string
    {
        return $this->module->getPublicVersion();
    }

    public function getSetupVersion(): string
    {
        return $this->module->getSetupVersion();
    }

    public function getEnvironment(): string
    {
        if ($this->moduleEnv->isProductionEnvironment()) {
            return self::ENVIRONMENT_PRODUCTION;
        }

        return self::ENVIRONMENT_DEVELOPMENT;
    }

    public function setEnvironment(string $env): void
    {
        if ($env === self::ENVIRONMENT_PRODUCTION) {
            $this->moduleEnv->enableProductionEnvironment();

            return;
        }

        $this->moduleEnv->enableDevelopmentEnvironment();
    }

    public function isProductionEnvironment(): bool
    {
        return $this->moduleEnv->isProductionEnvironment();
    }

    public function isDevelopmentEnvironment(): bool
    {
        return $this->moduleEnv->isDevelopmentEnvironment();
    }
}
