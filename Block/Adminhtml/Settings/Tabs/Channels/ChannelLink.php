<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Block\Adminhtml\Settings\Tabs\Channels;

class ChannelLink extends \Magento\Framework\Data\Form\Element\Link
{
    public function getElementHtml()
    {
        return sprintf(
            '<div style="margin-top: 5px;"><a id="%s" href="%s" target="_blank">%s</a> %s.</div>',
            $this->getHtmlId(),
            $this->getHref(),
            $this->getValue(),
            $this->getPostfix()
        );
    }
}
