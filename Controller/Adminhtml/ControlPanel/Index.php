<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Controller\Adminhtml\ControlPanel;

class Index extends \M2E\AmazonMcf\Controller\Adminhtml\ControlPanel\AbstractMain
{
    public function execute()
    {
        $block = $this->getLayout()->createBlock(\M2E\Core\Block\Adminhtml\ControlPanel\Tabs::class);
        $this->addContent($block);

        return $this->getResult();
    }
}
