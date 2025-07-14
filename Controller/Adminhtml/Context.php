<?php

namespace M2E\AmazonMcf\Controller\Adminhtml;

class Context extends \Magento\Backend\App\Action\Context
{
    /** @var \Magento\Framework\View\Result\PageFactory $resultPageFactory */
    private $resultPageFactory;
    /** @var \Magento\Framework\Controller\Result\RawFactory $resultRawFactory */
    private $resultRawFactory;
    /** @var \Magento\Framework\View\LayoutFactory $layoutFactory */
    private $layoutFactory;
    /** @var \M2E\AmazonMcf\Block\Adminhtml\Magento\Renderer\CssRenderer */
    private $cssRenderer;
    /** @var \Magento\Framework\App\ResourceConnection */
    private $resourceConnection;
    /** @var \Magento\Config\Model\Config */
    private $magentoConfig;

    public function __construct(
        \M2E\AmazonMcf\Block\Adminhtml\Magento\Renderer\CssRenderer $cssRenderer,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\ResponseInterface $response,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\App\ActionFlag $actionFlag,
        \Magento\Framework\App\ViewInterface $view,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Backend\Model\Session $session,
        \Magento\Framework\AuthorizationInterface $authorization,
        \Magento\Backend\Model\Auth $auth,
        \Magento\Backend\Helper\Data $helper,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Config\Model\Config $magentoConfig,
        $canUseBaseUrl = false
    ) {
        parent::__construct(
            $request,
            $response,
            $objectManager,
            $eventManager,
            $url,
            $redirect,
            $actionFlag,
            $view,
            $messageManager,
            $resultRedirectFactory,
            $resultFactory,
            $session,
            $authorization,
            $auth,
            $helper,
            $backendUrl,
            $formKeyValidator,
            $localeResolver,
            $canUseBaseUrl
        );

        $this->cssRenderer = $cssRenderer;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;
        $this->resourceConnection = $resourceConnection;
        $this->magentoConfig = $magentoConfig;
    }

    public function getResultPageFactory(): \Magento\Framework\View\Result\PageFactory
    {
        return $this->resultPageFactory;
    }

    public function getResultRawFactory(): \Magento\Framework\Controller\Result\RawFactory
    {
        return $this->resultRawFactory;
    }

    public function getLayoutFactory(): \Magento\Framework\View\LayoutFactory
    {
        return $this->layoutFactory;
    }

    public function getCssRenderer(): \M2E\AmazonMcf\Block\Adminhtml\Magento\Renderer\CssRenderer
    {
        return $this->cssRenderer;
    }

    public function getResourceConnection(): \Magento\Framework\App\ResourceConnection
    {
        return $this->resourceConnection;
    }

    public function getMagentoConfig(): \Magento\Config\Model\Config
    {
        return $this->magentoConfig;
    }
}
