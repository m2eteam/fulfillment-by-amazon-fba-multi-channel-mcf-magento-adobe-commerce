<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Dashboard\Order;

class CachedCalculator implements CalculatorInterface
{
    private const CACHE_LIFE_TIME = 600; // 10 min

    private Calculator $calculator;
    private \M2E\AmazonMcf\Helper\Data\Cache\Permanent $cache;

    public function __construct(
        Calculator $calculator,
        \M2E\AmazonMcf\Helper\Data\Cache\Permanent $cache
    ) {
        $this->calculator = $calculator;
        $this->cache = $cache;
    }

    public function getTotalCount(): int
    {
        return $this->getCachedValue(__METHOD__, function (): int {
            return $this->calculator->getTotalCount();
        });
    }

    public function getCountOfShippedToday(): int
    {
        return $this->getCachedValue(__METHOD__, function (): int {
            return $this->calculator->getCountOfShippedToday();
        });
    }

    public function getCountOfAllShipped(): int
    {
        return $this->getCachedValue(__METHOD__, function (): int {
            return $this->calculator->getCountOfAllShipped();
        });
    }

    public function getCountOfSkipped(): int
    {
        return $this->getCachedValue(__METHOD__, function (): int {
            return $this->calculator->getCountOfSkipped();
        });
    }

    public function getCountOfUnshipped(): int
    {
        return $this->getCachedValue(__METHOD__, function () {
            return $this->calculator->getCountOfUnshipped();
        });
    }

    private function getCachedValue(string $key, callable $handler): int
    {
        /** @var int|null $cachedValue */
        if ($cachedValue = $this->cache->getValue($key)) {
            return $cachedValue;
        }

        /** @var int $value */
        $value = $handler();
        $this->cache->setValue($key, $value, self::CACHE_LIFE_TIME);

        return $value;
    }
}
