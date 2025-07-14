<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Block\Adminhtml\ControlPanel;

class Open extends \Magento\Framework\View\Element\Template
{
    protected function _construct(): void
    {
        parent::_construct();

        $this->setId('controlPanelOpen');
        $this->setTemplate('M2E_AmazonMcf::control_panel/open.phtml');
    }

    public function getControlPanelUrl(): string
    {
        return $this->_urlBuilder->getUrl('*/controlPanel/index');
    }
}
