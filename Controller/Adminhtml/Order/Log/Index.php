<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Controller\Adminhtml\Order\Log;

class Index extends \M2E\AmazonMcf\Controller\Adminhtml\AbstractBase
{
    private \M2E\AmazonMcf\Controller\Adminhtml\AclAuthorization $aclAuthorization;
    private \M2E\AmazonMcf\Model\Order\Repository $orderRepository;

    public function __construct(
        \M2E\AmazonMcf\Model\Order\Repository $orderRepository,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \M2E\AmazonMcf\Controller\Adminhtml\AclAuthorization $aclAuthorization,
        \M2E\AmazonMcf\Controller\Adminhtml\Context $context
    ) {
        parent::__construct($context);

        $this->aclAuthorization = $aclAuthorization;
        $this->orderRepository = $orderRepository;
        $this->messageManager = $messageManager;
    }

    public function execute()
    {
        if ($id = $this->getRequest()->getParam('mcf_order_id')) {
            $order = $this->orderRepository->find((int)$id);
            if ($order === null) {
                $this->messageManager->addErrorMessage(
                    (string)__('Order does not exist.')
                );

                return $this->_redirect('*/*/*');
            }

            $title = __('Order #%1 Log', $order->getMagentoOrderIncrementId());
        } else {
            $title = __('Order Logs & Events');
        }

        $this->getResult()
             ->getConfig()
             ->getTitle()
             ->prepend((string)$title);

        return $this->getResult();
    }

    protected function _isAllowed(): bool
    {
        return $this->aclAuthorization->isAllowedGeneral();
    }
}
