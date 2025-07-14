<?php

namespace M2E\AmazonMcf\Block\Adminhtml;

/**
 * @method void setTooltiped()
 * @method void setNoHide()
 * @method void setNoCollapse()
 */
class HelpBlock extends \M2E\AmazonMcf\Block\Adminhtml\AbstractBlock
{
    protected $_template = 'M2E_AmazonMcf::help_block.phtml';

    public function getId(): string
    {
        if (null === $this->getData('id') && $this->getContent()) {
            $this->setData('id', 'block_notice_' . crc32($this->getContent()));
        }

        return $this->getData('id');
    }

    protected function _toHtml(): string
    {
        if ($this->getContent()) {
            return parent::_toHtml();
        }

        return '';
    }
}
