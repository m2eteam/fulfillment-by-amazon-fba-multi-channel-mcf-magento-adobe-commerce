<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Controller\Adminhtml\Order;

class Index extends \M2E\AmazonMcf\Controller\Adminhtml\AbstractBase
{
    private \M2E\AmazonMcf\Controller\Adminhtml\AclAuthorization $aclAuthorization;
    private \M2E\AmazonMcf\Model\Order\Repository $orderRepository;

    public function __construct(
        \M2E\AmazonMcf\Model\Order\Repository $orderRepository,
        \M2E\AmazonMcf\Controller\Adminhtml\AclAuthorization $aclAuthorization,
        \M2E\AmazonMcf\Controller\Adminhtml\Context $context
    ) {
        parent::__construct($context);

        $this->aclAuthorization = $aclAuthorization;
        $this->orderRepository = $orderRepository;
    }

    public function execute()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $order = $this->orderRepository->find((int)$id);
            if ($order === null) {
                $this->messageManager->addErrorMessage(
                    (string)__('Order does not exist.')
                );

                return $this->_redirect('*/*/*');
            }

            $title = __('Order #%1', $order->getMagentoOrderIncrementId());
        } else {
            $title = __('Orders');
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
