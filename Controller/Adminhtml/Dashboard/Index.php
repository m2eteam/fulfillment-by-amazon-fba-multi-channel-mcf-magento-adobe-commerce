<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Controller\Adminhtml\Dashboard;

class Index extends \M2E\AmazonMcf\Controller\Adminhtml\AbstractBase
{
    private \M2E\AmazonMcf\Block\Adminhtml\DashboardFactory $dashboardFactory;
    private \M2E\AmazonMcf\Model\Dashboard\Order\CachedCalculator $orderCachedCalculator;
    private \M2E\AmazonMcf\Model\Dashboard\Product\CachedCalculator $productCachedCalculator;
    private \M2E\AmazonMcf\Controller\Adminhtml\AclAuthorization $aclAuthorization;

    public function __construct(
        \M2E\AmazonMcf\Block\Adminhtml\DashboardFactory $dashboardFactory,
        \M2E\AmazonMcf\Model\Dashboard\Order\CachedCalculator $orderCachedCalculator,
        \M2E\AmazonMcf\Model\Dashboard\Product\CachedCalculator $productCachedCalculator,
        \M2E\AmazonMcf\Controller\Adminhtml\AclAuthorization $aclAuthorization,
        \M2E\AmazonMcf\Controller\Adminhtml\Context $context
    ) {
        parent::__construct($context);

        $this->aclAuthorization = $aclAuthorization;
        $this->dashboardFactory = $dashboardFactory;
        $this->orderCachedCalculator = $orderCachedCalculator;
        $this->productCachedCalculator = $productCachedCalculator;
    }

    public function execute(): \Magento\Framework\Controller\ResultInterface
    {
        $dashboard = $this->dashboardFactory->create(
            $this->orderCachedCalculator,
            $this->productCachedCalculator,
            $this->getLayout()
        );

        $this->addContent($dashboard);
        $this->getResultPage()->getConfig()->getTitle()->prepend((string)__('Dashboard'));

        return $this->getResult();
    }

    protected function _isAllowed(): bool
    {
        return $this->aclAuthorization->isAllowedGeneral();
    }
}
