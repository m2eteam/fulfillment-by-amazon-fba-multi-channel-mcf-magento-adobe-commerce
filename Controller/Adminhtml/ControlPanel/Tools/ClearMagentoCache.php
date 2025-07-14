<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Controller\Adminhtml\ControlPanel\Tools;

class ClearMagentoCache extends \M2E\AmazonMcf\Controller\Adminhtml\ControlPanel\AbstractMain
{
    private \M2E\AmazonMcf\Helper\Magento $magentoHelper;

    public function __construct(
        \M2E\AmazonMcf\Helper\Magento $magentoHelper,
        \M2E\AmazonMcf\Controller\Adminhtml\Context $context
    ) {
        parent::__construct($context);

        $this->magentoHelper = $magentoHelper;
    }

    public function execute()
    {
        $this->magentoHelper->clearCache();
        $this->getMessageManager()->addSuccessMessage('Magento cache was cleared.');
        $this->_redirect($this->_redirect->getRefererUrl());
    }
}
