<?php

namespace M2E\AmazonMcf\Controller\Adminhtml\Product\Action;

trait ActionTrait
{
    private function redirectToGrid(): \Magento\Framework\App\ResponseInterface
    {
        return $this->_redirect('*/product/index/');
    }
}
