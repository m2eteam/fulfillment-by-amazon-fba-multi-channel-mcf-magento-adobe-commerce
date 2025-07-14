<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Block\Adminhtml\Dashboard;

class Orders extends \M2E\AmazonMcf\Block\Adminhtml\AbstractBlock
{
    protected $_template = 'M2E_AmazonMcf::dashboard/orders.phtml';

    private \M2E\AmazonMcf\Model\Dashboard\Order\CalculatorInterface $orderCalculator;

    public function __construct(
        \M2E\AmazonMcf\Model\Dashboard\Order\CalculatorInterface $orderCalculator,
        \M2E\AmazonMcf\Model\Account\Repository $accountRepository,
        \M2E\AmazonMcf\Block\Adminhtml\Magento\Context\Template $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->orderCalculator = $orderCalculator;
    }

    public function getTotalCount(): int
    {
        return $this->orderCalculator->getTotalCount();
    }

    public function getCountOfShippedToday(): int
    {
        return $this->orderCalculator->getCountOfShippedToday();
    }

    public function getCountOfAllShipped(): int
    {
        return $this->orderCalculator->getCountOfAllShipped();
    }

    public function getCountOfSkipped(): int
    {
        return $this->orderCalculator->getCountOfSkipped();
    }

    public function getCountOfUnshipped(): int
    {
        return $this->orderCalculator->getCountOfUnshipped();
    }
}
