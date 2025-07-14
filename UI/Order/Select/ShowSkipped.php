<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\UI\Order\Select;

class ShowSkipped implements \Magento\Framework\Data\OptionSourceInterface
{
    public const OPTION_VALUE_SHOW = 'show';

    public function toOptionArray(): array
    {
        return [
            ['label' => __('Hide'), 'value' => ''],
            ['label' => __('Show'), 'value' => self::OPTION_VALUE_SHOW],
        ];
    }
}
