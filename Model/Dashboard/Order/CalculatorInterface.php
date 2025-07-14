<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Dashboard\Order;

interface CalculatorInterface
{
    public function getTotalCount(): int;

    public function getCountOfShippedToday(): int;

    public function getCountOfAllShipped(): int;

    public function getCountOfSkipped(): int;

    public function getCountOfUnshipped(): int;
}
