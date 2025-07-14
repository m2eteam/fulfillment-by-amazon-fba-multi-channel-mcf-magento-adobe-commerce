<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Controller\Adminhtml\Product\Log;

class Index extends \M2E\AmazonMcf\Controller\Adminhtml\AbstractBase
{
    private \M2E\AmazonMcf\Controller\Adminhtml\AclAuthorization $aclAuthorization;
    private \M2E\AmazonMcf\Model\Product\Repository $productRepository;

    public function __construct(
        \M2E\AmazonMcf\Model\Product\Repository $productRepository,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \M2E\AmazonMcf\Controller\Adminhtml\AclAuthorization $aclAuthorization,
        \M2E\AmazonMcf\Controller\Adminhtml\Context $context
    ) {
        parent::__construct($context);

        $this->aclAuthorization = $aclAuthorization;
        $this->productRepository = $productRepository;
        $this->messageManager = $messageManager;
    }

    public function execute()
    {
        if ($id = $this->getRequest()->getParam('mcf_product_id')) {
            $product = $this->productRepository->find((int)$id);
            if ($product === null) {
                $this->messageManager->addErrorMessage(
                    (string)__('Product does not exist.')
                );

                return $this->_redirect('*/*/*');
            }

            $title = __('Product #%1 Log', $product->getMagentoProductId());
        } else {
            $title = __('Product Logs & Events');
        }

        $this->getResult()
             ->getConfig()
             ->getTitle()
             ->set((string)$title);

        return $this->getResult();
    }

    protected function _isAllowed(): bool
    {
        return $this->aclAuthorization->isAllowedGeneral();
    }
}
