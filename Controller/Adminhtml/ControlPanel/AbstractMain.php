<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Controller\Adminhtml\ControlPanel;

abstract class AbstractMain extends \M2E\AmazonMcf\Controller\Adminhtml\AbstractBase
{
    public function _isAllowed(): bool
    {
        return true;
    }

    protected function _validateSecretKey(): bool
    {
        return true;
    }

    protected function preDispatch(\Magento\Framework\App\RequestInterface $request)
    {
        return true;
    }
}
