<?php

namespace M2E\AmazonMcf\Block\Adminhtml\Magento\Context;

use Magento\Backend\Block\Template\Context;
use M2E\AmazonMcf\Block\Adminhtml\Traits;
use M2E\AmazonMcf\Block\Adminhtml\Magento\Renderer;

class Template extends Context
{
    use Traits\RendererTrait;

    private \Magento\Framework\Data\Form\Element\Factory $elementFactory;
    private \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig;

    public function __construct(
        Renderer\CssRenderer $css,
        Renderer\JsPhpRenderer $jsPhp,
        Renderer\JsRenderer $js,
        Renderer\JsTranslatorRenderer $jsTranslatorRenderer,
        Renderer\JsUrlRenderer $jsUrlRenderer,
        \Magento\Framework\Data\Form\Element\Factory $elementFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Framework\View\DesignInterface $design,
        \Magento\Framework\Session\Generic $session,
        \Magento\Framework\Session\SidResolverInterface $sidResolver,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\View\ConfigInterface $viewConfig,
        \Magento\Framework\App\Cache\StateInterface $cacheState,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Filter\FilterManager $filterManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\View\FileSystem $viewFileSystem,
        \Magento\Framework\View\TemplateEnginePool $enginePool,
        \Magento\Framework\App\State $appState,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Page\Config $pageConfig,
        \Magento\Framework\View\Element\Template\File\Resolver $resolver,
        \Magento\Framework\View\Element\Template\File\Validator $validator,
        \Magento\Framework\AuthorizationInterface $authorization,
        \Magento\Backend\Model\Session $backendSession,
        \Magento\Framework\Math\Random $mathRandom,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Framework\Code\NameBuilder $nameBuilder,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
    ) {
        $this->wysiwygConfig = $wysiwygConfig;

        $this->css = $css;
        $this->jsPhp = $jsPhp;
        $this->js = $js;
        $this->jsTranslator = $jsTranslatorRenderer;
        $this->jsUrl = $jsUrlRenderer;

        $this->elementFactory = $elementFactory;

        parent::__construct(
            $request,
            $layout,
            $eventManager,
            $urlBuilder,
            $cache,
            $design,
            $session,
            $sidResolver,
            $scopeConfig,
            $assetRepo,
            $viewConfig,
            $cacheState,
            $logger,
            $escaper,
            $filterManager,
            $localeDate,
            $inlineTranslation,
            $filesystem,
            $viewFileSystem,
            $enginePool,
            $appState,
            $storeManager,
            $pageConfig,
            $resolver,
            $validator,
            $authorization,
            $backendSession,
            $mathRandom,
            $formKey,
            $nameBuilder
        );
    }

    public function getJsPhp(): \M2E\AmazonMcf\Block\Adminhtml\Magento\Renderer\JsPhpRenderer
    {
        return $this->jsPhp;
    }

    public function getJsTranslator(): \M2E\AmazonMcf\Block\Adminhtml\Magento\Renderer\JsTranslatorRenderer
    {
        return $this->jsTranslator;
    }

    public function getJsUrl(): \M2E\AmazonMcf\Block\Adminhtml\Magento\Renderer\JsUrlRenderer
    {
        return $this->jsUrl;
    }

    public function getJs(): \M2E\AmazonMcf\Block\Adminhtml\Magento\Renderer\JsRenderer
    {
        return $this->js;
    }

    public function getCss(): \M2E\AmazonMcf\Block\Adminhtml\Magento\Renderer\CssRenderer
    {
        return $this->css;
    }

    public function getElementFactory(): \Magento\Framework\Data\Form\Element\Factory
    {
        return $this->elementFactory;
    }

    public function getWysiwygConfig(): \Magento\Cms\Model\Wysiwyg\Config
    {
        return $this->wysiwygConfig;
    }
}
