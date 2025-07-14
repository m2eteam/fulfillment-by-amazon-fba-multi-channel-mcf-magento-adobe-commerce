<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Block\Adminhtml\Dashboard;

class Products extends \M2E\AmazonMcf\Block\Adminhtml\AbstractBlock
{
    protected $_template = 'M2E_AmazonMcf::dashboard/products.phtml';

    private \M2E\AmazonMcf\Model\Dashboard\Product\CalculatorInterface $productCalculator;
    private \M2E\AmazonMcf\Model\Account\Repository $accountRepository;
    /** @var Products\Row[]|null  */
    private ?array $rows = null;

    public function __construct(
        \M2E\AmazonMcf\Model\Dashboard\Product\CalculatorInterface $productCalculator,
        \M2E\AmazonMcf\Model\Account\Repository $accountRepository,
        \M2E\AmazonMcf\Block\Adminhtml\Magento\Context\Template $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->productCalculator = $productCalculator;
        $this->accountRepository = $accountRepository;
    }

    /**
     * @return Products\Row[]
     */
    public function getRows(): array
    {
        if ($this->rows !== null) {
            return $this->rows;
        }

        $rows = [];
        foreach ($this->accountRepository->getAll() as $account) {
            $rows[] = new Products\Row(
                $this->productCalculator->getTotalCount($account),
                $this->productCalculator->getCountOfAvailable($account),
                $this->productCalculator->getCountOfEnabled($account),
                $account
            );
        }

        return $this->rows = $rows;
    }
}
