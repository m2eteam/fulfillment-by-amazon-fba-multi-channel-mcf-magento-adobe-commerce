<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Controller\Adminhtml\ControlPanel\Tools;

class ChangeMaintenanceMode extends \M2E\AmazonMcf\Controller\Adminhtml\ControlPanel\AbstractMain
{
    private \M2E\AmazonMcf\Helper\Module\Maintenance $maintenance;

    public function __construct(
        \M2E\AmazonMcf\Helper\Module\Maintenance $maintenance,
        \M2E\AmazonMcf\Controller\Adminhtml\Context $context
    ) {
        parent::__construct($context);

        $this->maintenance = $maintenance;
    }

    public function execute()
    {
        if ($this->maintenance->isEnabled()) {
            $this->maintenance->disable();
        } else {
            $this->maintenance->enable();
        }

        $this->messageManager->addSuccessMessage('Maintenance Mode Changed.');

        return $this->_redirect($this->_redirect->getRefererUrl());
    }
}
