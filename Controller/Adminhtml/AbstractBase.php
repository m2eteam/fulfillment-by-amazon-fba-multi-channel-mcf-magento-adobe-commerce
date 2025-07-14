<?php

namespace M2E\AmazonMcf\Controller\Adminhtml;

abstract class AbstractBase extends \Magento\Backend\App\Action
{
    public const LAYOUT_ONE_COLUMN = '1column';
    public const LAYOUT_TWO_COLUMNS = '2columns';
    public const LAYOUT_BLANK = 'blank';

    public const GLOBAL_MESSAGES_GROUP = 'amazonmcf_global_messages_group';

    private \Magento\Framework\View\Result\PageFactory $resultPageFactory;
    private \Magento\Framework\Controller\Result\RawFactory $resultRawFactory;
    private \Magento\Framework\View\LayoutFactory $layoutFactory;
    private \M2E\AmazonMcf\Block\Adminhtml\Magento\Renderer\CssRenderer $cssRenderer;
    private \Magento\Framework\App\Response\RedirectInterface $redirect;
    private bool $generalBlockWasAppended = false;
    private ?\Magento\Framework\View\LayoutInterface $emptyLayout = null;
    private ?\Magento\Framework\Controller\Result\Raw $rawResult = null;
    private ?\Magento\Framework\View\Result\Page $resultPage = null;

    public function __construct(\M2E\AmazonMcf\Controller\Adminhtml\Context $context)
    {
        $this->resultPageFactory = $context->getResultPageFactory();
        $this->resultRawFactory = $context->getResultRawFactory();
        $this->layoutFactory = $context->getLayoutFactory();
        $this->cssRenderer = $context->getCssRenderer();
        $this->redirect = $context->getRedirect();

        parent::__construct($context);
    }

    // ----------------------------------------

    protected function _isAllowed()
    {
        return $this->_auth->isLoggedIn();
    }

    // ----------------------------------------

    protected function isAjax(?\Magento\Framework\App\RequestInterface $request = null)
    {
        if ($request === null) {
            $request = $this->getRequest();
        }

        return $request->isXmlHttpRequest() || $request->getParam('isAjax');
    }

    // ----------------------------------------

    protected function getLayoutType()
    {
        return self::LAYOUT_ONE_COLUMN;
    }

    // ----------------------------------------

    public function getMessageManager()
    {
        return $this->messageManager;
    }

    // ----------------------------------------

    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $preDispatchResult = $this->preDispatch($request);
        if ($preDispatchResult !== true) {
            return $preDispatchResult;
        }

        try {
            $result = parent::dispatch($request);
        } catch (\Throwable $exception) {
            /** @var \M2E\AmazonMcf\Helper\Module $moduleHelper */
            $moduleHelper = $this->_objectManager->get(\M2E\AmazonMcf\Helper\Module::class);
            if ($moduleHelper->isDevelopmentEnvironment()) {
                throw $exception;
            }

            /** @var \M2E\AmazonMcf\Model\SystemLog\Logger $systemLogger */
            $systemLogger = $this->_objectManager->get(\M2E\AmazonMcf\Model\SystemLog\Logger::class);
            $systemLogger->exception($exception);

            /** @psalm-suppress UndefinedInterfaceMethod */
            if ($request->isXmlHttpRequest() || $request->getParam('isAjax')) {
                $this->getRawResult()->setContents($exception->getMessage());

                return $this->getRawResult();
            }

            $this->getMessageManager()->addErrorMessage(
                (string)__('Fatal error occurred: "%message".', ['message' => $exception->getMessage()]),
            );

            return $this->_redirect('admin/dashboard');
        }

        $this->postDispatch($request);

        return $result;
    }

    // ---------------------------------------

    protected function preDispatch(\Magento\Framework\App\RequestInterface $request)
    {
        /** @var \M2E\AmazonMcf\Helper\Module\Maintenance $maintenance */
        $maintenance = $this->_objectManager->get(\M2E\AmazonMcf\Helper\Module\Maintenance::class);
        if ($maintenance->isEnabled()) {
            return $this->_redirect('*/maintenance/index');
        }

        /** @var \M2E\AmazonMcf\Model\Module $module */
        $module = $this->_objectManager->get(\M2E\AmazonMcf\Model\Module::class);
        if ($module->isDisabled()) {
            $message = (string)__(
                'M2E Amazon MCF is currently disabled. The module interface is unavailable.'
                . ' To enable the module, go to  <i>Stores > Settings > Configuration > M2E Amazon MCF > Module</i>.'
            );
            $this->getMessageManager()->addNoticeMessage($message);

            return $this->_redirect('admin/dashboard');
        }

        if ($this->isAjax($request) && !$this->_auth->isLoggedIn()) {
            $this->getRawResult()->setContents(
                json_encode([
                    'ajaxExpired' => 1,
                    'ajaxRedirect' => $this->redirect->getRefererUrl(),
                ])
            );

            return $this->getRawResult();
        }

        return true;
    }

    protected function postDispatch(\Magento\Framework\App\RequestInterface $request)
    {
        ob_get_clean();

        if ($this->isAjax($request)) {
            return;
        }

        if ($this->getLayoutType() === self::LAYOUT_BLANK) {
            $this->addCss('layout/blank.css');
        }

        foreach ($this->cssRenderer->getFiles() as $file) {
            $this->addCss($file);
        }

        $this->activateMenuItem();
    }

    private function activateMenuItem(): void
    {
        if ($this->_view->getLayout()->getBlock('menu')) {
            $this->_setActiveMenu('M2E_AmazonMcf::general');
        }
    }

    // ----------------------------------------

    protected function getLayout()
    {
        if ($this->isAjax()) {
            $this->initEmptyLayout();

            return $this->emptyLayout;
        }

        return $this->getResultPage()->getLayout();
    }

    protected function initEmptyLayout()
    {
        if ($this->emptyLayout !== null) {
            return;
        }

        $this->emptyLayout = $this->layoutFactory->create();
    }

    // ---------------------------------------

    protected function getResult()
    {
        if ($this->isAjax()) {
            return $this->getRawResult();
        }

        return $this->getResultPage();
    }

    // ---------------------------------------

    protected function getResultPage()
    {
        if ($this->resultPage === null) {
            $this->initResultPage();
        }

        return $this->resultPage;
    }

    protected function initResultPage()
    {
        if ($this->resultPage !== null) {
            return;
        }

        $this->resultPage = $this->resultPageFactory->create();
        $this->resultPage->addHandle($this->getLayoutType());

        $this->resultPage->getConfig()->getTitle()->set((string)__('M2E Amazon MCF'));
    }

    // ---------------------------------------

    protected function getRawResult()
    {
        if ($this->rawResult === null) {
            $this->initRawResult();
        }

        return $this->rawResult;
    }

    protected function initRawResult()
    {
        if ($this->rawResult !== null) {
            return;
        }

        $this->rawResult = $this->resultRawFactory->create();
    }

    // ----------------------------------------

    protected function addLeft(\Magento\Framework\View\Element\AbstractBlock $block)
    {
        if ($this->getLayoutType() != self::LAYOUT_TWO_COLUMNS) {
            throw new \LogicException('Add left can not be used for non two column layout');
        }

        $this->initResultPage();
        $this->appendGeneralBlock();

        return $this->_addLeft($block);
    }

    /**
     * @param \Magento\Framework\View\Element\AbstractBlock|\Magento\Framework\View\Element\BlockInterface $block
     */
    protected function addContent(\Magento\Framework\View\Element\AbstractBlock $block): void
    {
        $this->initResultPage();
        $this->appendGeneralBlock();
        $this->_addContent($block);
    }

    protected function setAjaxContent($blockData)
    {
        if ($blockData instanceof \Magento\Framework\View\Element\AbstractBlock) {
            $blockData = $blockData->toHtml();
        }

        $this->getRawResult()->setContents($blockData);
    }

    // ---------------------------------------

    protected function addCss($file)
    {
        $this->getResultPage()->getConfig()->addPageAsset("M2E_AmazonMcf::css/$file");
    }

    // ----------------------------------------

    protected function appendGeneralBlock()
    {
        if ($this->generalBlockWasAppended) {
            return;
        }

        $generalBlock = $this->getLayout()->createBlock(\M2E\AmazonMcf\Block\Adminhtml\General::class);
        $this->getLayout()->setChild('js', $generalBlock->getNameInLayout(), '');

        $this->generalBlockWasAppended = true;
    }

    // ----------------------------------------

    protected function getRequestIds($key = 'id')
    {
        $id = $this->getRequest()->getParam($key);
        $ids = $this->getRequest()->getParam($key . 's');

        if ($id === null && $ids === null) {
            return [];
        }

        $requestIds = [];

        if ($ids !== null) {
            if (is_string($ids)) {
                $ids = explode(',', $ids);
            }
            $requestIds = (array)$ids;
        }

        if ($id !== null) {
            $requestIds[] = $id;
        }

        return array_filter($requestIds);
    }

    // ----------------------------------------

    protected function setPageHelpLink($link)
    {
        /** @var \Magento\Theme\Block\Html\Title $pageTitleBlock */
        $pageTitleBlock = $this->getLayout()->getBlock('page.title');

        /** @var \M2E\AmazonMcf\Block\Adminhtml\PageHelpLink $helpLinkBlock */
        $helpLinkBlock = $this->getLayout()
                              ->createBlock(\M2E\AmazonMcf\Block\Adminhtml\PageHelpLink::class)
                              ->setData([
                                  'page_help_link' => $link,
                              ]);

        $pageTitleBlock->setTitleClass('amazonmcf-page-title');
        $pageTitleBlock->setChild('amazonmcf.page.help.block', $helpLinkBlock);
    }

    // ----------------------------------------

    /**
     * Clears global messages session to prevent duplicate
     * @inheritdoc
     */
    protected function _redirect($path, $arguments = [])
    {
        $this->messageManager->getMessages(true, self::GLOBAL_MESSAGES_GROUP);

        return parent::_redirect($path, $arguments);
    }
}
