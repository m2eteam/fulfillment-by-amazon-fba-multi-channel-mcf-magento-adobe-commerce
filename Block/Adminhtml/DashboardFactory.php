<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Block\Adminhtml;

class DashboardFactory
{
    public function create(
        \M2E\AmazonMcf\Model\Dashboard\Order\CalculatorInterface $orderCalculator,
        \M2E\AmazonMcf\Model\Dashboard\Product\CalculatorInterface $productCalculator,
        \Magento\Framework\View\LayoutInterface $layout
    ): Dashboard {
        /** @var \M2E\AmazonMcf\Block\Adminhtml\Dashboard\Products $products */
        $products = $layout->createBlock(Dashboard\Products::class, 'dashboard_products', [
            'productCalculator' => $productCalculator,
        ]);

        $orders = $layout->createBlock(Dashboard\Orders::class, 'dashboard_orders', [
            'orderCalculator' => $orderCalculator,
        ]);

        /** @var Dashboard */
        return $layout->createBlock(Dashboard::class, 'dashboard', [
            'products' => $products,
            'orders' => $orders,
        ]);
    }
}
