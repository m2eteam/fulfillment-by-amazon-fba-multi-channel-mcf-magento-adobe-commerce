<?php

namespace M2E\AmazonMcf\Helper;

class Magento
{
    public const MAGENTO_INVENTORY_MODULE_NICK = 'Magento_Inventory';

    private \Magento\Framework\App\ProductMetadataInterface $productMetadata;
    private \Magento\Framework\App\ResourceConnection $resource;
    private \Magento\Framework\Module\ModuleListInterface $moduleList;
    private \Magento\Framework\App\DeploymentConfig $deploymentConfig;
    private \Magento\Framework\Locale\ResolverInterface $localeResolver;
    private \Magento\Framework\App\State $appState;
    private \Magento\Framework\App\CacheInterface $appCache;
    /** @var \Magento\Framework\UrlInterface|\Magento\Backend\Model\UrlInterface */
    private $urlBuilder;
    private \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\App\DeploymentConfig $deploymentConfig,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Framework\App\State $appState,
        \Magento\Framework\App\CacheInterface $appCache
    ) {
        $this->productMetadata = $productMetadata;
        $this->resource = $resource;
        $this->moduleList = $moduleList;
        $this->deploymentConfig = $deploymentConfig;
        $this->localeResolver = $localeResolver;
        $this->appState = $appState;
        $this->appCache = $appCache;
        $this->urlBuilder = $urlBuilder;
        $this->scopeConfig = $scopeConfig;
    }

    public function getName(): string
    {
        return 'magento';
    }

    public function getVersion(bool $asArray = false)
    {
        $versionString = $this->productMetadata->getVersion();

        return $asArray ? explode('.', $versionString) : $versionString;
    }

    public function isMSISupportingVersion(): bool
    {
        return $this->moduleList->getOne(self::MAGENTO_INVENTORY_MODULE_NICK) !== null;
    }

    public function getEditionName(): string
    {
        return strtolower($this->productMetadata->getEdition());
    }

    public function isDefault(): bool
    {
        return $this->appState->getMode() === \Magento\Framework\App\State::MODE_DEFAULT;
    }

    // ---------------------------------------

    /**
     * @return string
     */
    public function getBaseUrl(): string
    {
        return str_replace('index.php/', '', $this->urlBuilder->getBaseUrl());
    }

    public function getLocale()
    {
        return $this->localeResolver->getLocale();
    }

    public function getDefaultLocale()
    {
        return $this->localeResolver->getDefaultLocale();
    }

    public function getBaseCurrency()
    {
        return (string)$this->scopeConfig->getValue(
            \Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE,
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    // ----------------------------------------

    /**
     * @return array
     */
    public function getMySqlTables(): array
    {
        return $this->resource->getConnection()->listTables();
    }

    // ---------------------------------------

    public function getDatabaseName(): string
    {
        return (string)$this->deploymentConfig->get(
            \Magento\Framework\Config\ConfigOptionsListConstants::CONFIG_PATH_DB_CONNECTION_DEFAULT
            . '/dbname'
        );
    }

    public function getDatabaseTablesPrefix()
    {
        return (string)$this->deploymentConfig->get(
            \Magento\Framework\Config\ConfigOptionsListConstants::CONFIG_PATH_DB_PREFIX
        );
    }

    // ----------------------------------------

    public function isInstalled(): bool
    {
        return $this->deploymentConfig->isAvailable();
    }

    // ----------------------------------------

    public function clearCache()
    {
        return $this->appCache->clean();
    }
}
