<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Block\Adminhtml;

class Dashboard extends AbstractContainer
{
    /** @var string */
    protected $_template = 'M2E_AmazonMcf::dashboard.phtml';

    private Dashboard\Orders $orders;
    private Dashboard\Products $products;
    private \M2E\AmazonMcf\Model\Account\ForceSyncConfig $forceSyncConfig;
    private \M2E\AmazonMcf\Helper\M2EPro\Url $urlHelper;
    private \M2E\AmazonMcf\Helper\M2EPro $m2eproHelper;

    public function __construct(
        Dashboard\Orders $orders,
        Dashboard\Products $products,
        \M2E\AmazonMcf\Helper\M2EPro $m2eproHelper,
        \M2E\AmazonMcf\Model\Account\ForceSyncConfig $forceSyncConfig,
        \M2E\AmazonMcf\Helper\M2EPro\Url $urlHelper,
        \M2E\AmazonMcf\Helper\Support $supportHelper,
        \M2E\AmazonMcf\Block\Adminhtml\Magento\Context\Widget $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->orders = $orders;
        $this->products = $products;
        $this->m2eproHelper = $m2eproHelper;
        $this->forceSyncConfig = $forceSyncConfig;
        $this->urlHelper = $urlHelper;
    }

    protected function _construct()
    {
        $this->addButton('mcf_view_items', [
            'label' => $this->__('View Items'),
            'onclick' => sprintf("setLocation('%s');", $this->getUrl('*/product/index')),
            'class' => 'primary',
        ]);

        $this->addButton('mcf_view_orders', [
            'label' => $this->__('View Orders'),
            'onclick' => sprintf("setLocation('%s');", $this->getUrl('*/order/index')),
            'class' => 'primary',
        ]);

        $this->addButton('mcf_logs_and_events', [
            'label' => $this->__('Logs & Events'),
            'class' => 'primary',
            'class_name' => \M2E\AmazonMcf\Block\Adminhtml\Magento\Button\SplitButton::class,
            'options' => [
                'items' => [
                    'label' => __('Items'),
                    'onclick' => 'setLocation(this.getAttribute("data-url"))',
                    'data_attribute' => [
                        'url' => $this->getUrl('*/product_log/index'),
                    ],
                ],
                'orders' => [
                    'label' => __('Orders'),
                    'onclick' => 'setLocation(this.getAttribute("data-url"))',
                    'data_attribute' => [
                        'url' => $this->getUrl('*/order_log/index'),
                    ],
                ],
            ],
        ]);

        parent::_construct();
    }

    protected function _prepareLayout()
    {
        $this->css->addFile('dashboard/view.css');

        return parent::_prepareLayout();
    }

    public function isClean(): bool
    {
        return empty($this->products->getRows());
    }

    public function getOrders(): Dashboard\Orders
    {
        return $this->orders;
    }

    public function getProducts(): Dashboard\Products
    {
        return $this->products;
    }

    public function getUrlAccountForceSync(): string
    {
        return $this->getUrl('*/account_sync/forceSync/index');
    }

    public function getUrlSettings(): string
    {
        return $this->getUrl('*/settings/index');
    }

    public function isNeedAccountForceSync(): bool
    {
        return $this->m2eproHelper->isModuleEnabled()
            && $this->forceSyncConfig->isEnabled();
    }

    public function getM2EProAmazonAccountsUrl(): string
    {
        return $this->urlHelper->getAmazonAccountsUrl();
    }

    public function getSupportEmail(): string
    {
        return \M2E\AmazonMcf\Helper\Support::EMAIL;
    }
}
