<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Controller\Adminhtml\Maintenance;

class Index extends \Magento\Backend\App\Action
{
    private \Magento\Framework\View\Result\PageFactory $pageFactory;
    private \M2E\AmazonMcf\Helper\Module\Maintenance $maintenance;

    public function __construct(
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \M2E\AmazonMcf\Helper\Module\Maintenance $maintenanceConfig,
        \Magento\Backend\App\Action\Context $context
    ) {
        parent::__construct($context);

        $this->pageFactory = $pageFactory;
        $this->maintenance = $maintenanceConfig;
    }

    public function execute()
    {
        if (!$this->maintenance->isEnabled()) {
            return $this->_redirect('admin');
        }

        $result = $this->pageFactory->create();

        $result->getConfig()->getTitle()->set(
            __('M2E Amazon MCF is currently under maintenance')
        );
        $this->_setActiveMenu('M2E_AmazonMcf::general');

        /** @var \Magento\Framework\View\Element\Template $block */
        $block = $result->getLayout()->createBlock(\Magento\Framework\View\Element\Template::class);
        $block->setTemplate('M2E_AmazonMcf::maintenance.phtml');
        $block->setData('supportEmail', \M2E\AmazonMcf\Helper\Support::EMAIL);
        $this->_addContent($block);

        /** @var \M2E\AmazonMcf\Block\Adminhtml\ControlPanel\Open $openControlPanel */
        $openControlPanel = $result->getLayout()->createBlock(\M2E\AmazonMcf\Block\Adminhtml\ControlPanel\Open::class);
        $this->_addContent($openControlPanel);

        return $result;
    }
}
