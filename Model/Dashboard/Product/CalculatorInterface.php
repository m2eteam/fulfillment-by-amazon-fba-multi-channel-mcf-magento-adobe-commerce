<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Dashboard\Product;

interface CalculatorInterface
{
    public function getTotalCount(\M2E\AmazonMcf\Model\Account $account): int;

    public function getCountOfAvailable(\M2E\AmazonMcf\Model\Account $account): int;

    public function getCountOfEnabled(\M2E\AmazonMcf\Model\Account $account): int;
}
