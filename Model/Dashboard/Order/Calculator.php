<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Dashboard\Order;

class Calculator implements CalculatorInterface
{
    private \M2E\AmazonMcf\Model\Order\Repository $repository;

    public function __construct(\M2E\AmazonMcf\Model\Order\Repository $repository)
    {
        $this->repository = $repository;
    }

    public function getTotalCount(): int
    {
        return $this->repository->getTotalCount();
    }

    public function getCountOfShippedToday(): int
    {
        $dateStart = \M2E\Core\Helper\Date::createCurrentInCurrentZone();
        $dateStart->setTime(0, 0);

        $dateEnd = \M2E\Core\Helper\Date::createCurrentInCurrentZone();
        $dateEnd->setTime(23, 59, 59);

        return $this->repository->getCountOfShippedAndCompletedByDateRange($dateStart, $dateEnd);
    }

    public function getCountOfAllShipped(): int
    {
        return $this->repository->getCountOfAllShippedAndCompleted();
    }

    public function getCountOfSkipped(): int
    {
        return $this->repository->getCountOfSkipped();
    }

    public function getCountOfUnshipped(): int
    {
        return $this->repository->getCountOfAllUnshipped();
    }
}
