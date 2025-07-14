<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Block\Adminhtml\Dashboard\Products;

class Row
{
    private int $countOfTotalItems;
    private int $countOfAvailableItems;
    private int $countOfEnabledItems;
    private \M2E\AmazonMcf\Model\Account $account;

    public function __construct(
        int $totalCountOfItems,
        int $countOfAvailableItems,
        int $countOfEnabledItems,
        \M2E\AmazonMcf\Model\Account $account
    ) {
        $this->countOfTotalItems = $totalCountOfItems;
        $this->countOfAvailableItems = $countOfAvailableItems;
        $this->countOfEnabledItems = $countOfEnabledItems;
        $this->account = $account;
    }

    public function getCountOfTotalItems(): int
    {
        return $this->countOfTotalItems;
    }

    public function getCountOfAvailableItems(): int
    {
        return $this->countOfAvailableItems;
    }

    public function getCountOfEnabledItems(): int
    {
        return $this->countOfEnabledItems;
    }

    public function getAccount(): \M2E\AmazonMcf\Model\Account
    {
        return $this->account;
    }
}
