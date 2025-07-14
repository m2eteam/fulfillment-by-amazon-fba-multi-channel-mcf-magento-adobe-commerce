<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Dashboard\Product;

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

    public function getTotalCount(\M2E\AmazonMcf\Model\Account $account): int
    {
        return $this->getCachedValue(__METHOD__, $account, function (\M2E\AmazonMcf\Model\Account $account): int {
            return $this->calculator->getTotalCount($account);
        });
    }

    public function getCountOfAvailable(\M2E\AmazonMcf\Model\Account $account): int
    {
        return $this->getCachedValue(__METHOD__, $account, function (\M2E\AmazonMcf\Model\Account $account): int {
            return $this->calculator->getCountOfAvailable($account);
        });
    }

    public function getCountOfEnabled(\M2E\AmazonMcf\Model\Account $account): int
    {
        return $this->getCachedValue(__METHOD__, $account, function (\M2E\AmazonMcf\Model\Account $account): int {
            return $this->calculator->getCountOfEnabled($account);
        });
    }

    private function getCachedValue(
        string $key,
        \M2E\AmazonMcf\Model\Account $account,
        callable $handler
    ): int {
        $key = $key . '_' . $account->getMerchantId();

        /** @var int|null $cachedValue */
        if ($cachedValue = $this->cache->getValue($key)) {
            return $cachedValue;
        }

        /** @var int $value */
        $value = $handler($account);
        $this->cache->setValue($key, $value, self::CACHE_LIFE_TIME);

        return $value;
    }
}
