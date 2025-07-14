<?php

namespace M2E\AmazonMcf\Block\Adminhtml;

class PageHelpLink extends \M2E\AmazonMcf\Block\Adminhtml\AbstractBlock
{
    protected $_template = 'page_help_link.phtml';

    protected function _toHtml()
    {
        if ($this->getPageHelpLink() === null) {
            return '';
        }

        return parent::_toHtml();
    }
}
