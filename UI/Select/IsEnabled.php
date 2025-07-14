<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\UI\Select;

class IsEnabled implements \Magento\Framework\Data\OptionSourceInterface
{
    public function toOptionArray(): array
    {
        $attribute = [
            (string)__('Disabled'),
            (string)__('Enabled'),
        ];

        $options = [];
        foreach ($attribute as $value => $label) {
            $options[] = [
                'label' => $label,
                'value' => $value,
            ];
        }

        return $options;
    }
}
