<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\UI\Order\DataProvider;

use M2E\AmazonMcf\UI\Order\Select\ShowSkipped as Select;

class FilterManager
{
    private const FILTER_FIELD_DEFAULT = 'default';
    private const FILTER_FIELD_SHOW_SKIPPED = 'show_skipped';

    private const IGNORE_FIELDS = [
        self::FILTER_FIELD_DEFAULT,
        self::FILTER_FIELD_SHOW_SKIPPED,
    ];

    private bool $flagHideSkipped = true;

    public function isDefaultFilter(string $field): bool
    {
        return $field === self::FILTER_FIELD_DEFAULT;
    }

    public function getDefaultFilter(\Magento\Framework\Api\FilterBuilder $filterBuilder): \Magento\Framework\Api\Filter
    {
        return $filterBuilder->setField(self::FILTER_FIELD_DEFAULT)
                             ->create();
    }

    // ---------------------------------------

    public function isNeedToHideSkipped(): bool
    {
        return $this->flagHideSkipped;
    }

    public function decideToHideSkipped(\Magento\Framework\App\RequestInterface $request): void
    {
        $this->flagHideSkipped = $this->getValueForHideSkipped(
            $request->getParam('filters', []),
        );
    }

    private function getValueForHideSkipped(array $filters): bool
    {
        $hide = true;
        if (
            isset($filters[self::FILTER_FIELD_SHOW_SKIPPED])
            && $filters[self::FILTER_FIELD_SHOW_SKIPPED] === Select::OPTION_VALUE_SHOW
        ) {
            $hide = false;
        }

        if (isset($filters['status'])) {
            $hide = (int)$filters['status'] !== \M2E\AmazonMcf\Model\Order::STATUS_SKIPPED;
        }

        return $hide;
    }

    // ---------------------------------------

    public function isNeedToIgnoreFilter(string $field): bool
    {
        return in_array($field, self::IGNORE_FIELDS);
    }
}
