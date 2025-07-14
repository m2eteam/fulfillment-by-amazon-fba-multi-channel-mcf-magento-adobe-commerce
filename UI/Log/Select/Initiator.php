<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\UI\Log\Select;

class Initiator implements \Magento\Framework\Data\OptionSourceInterface
{
    public function toOptionArray(): array
    {
        $initiators = [
            \M2E\AmazonMcf\Model\Logger\Initiator::INITIATOR_UNKNOWN => (string)__('Unknown'),
            \M2E\AmazonMcf\Model\Logger\Initiator::INITIATOR_USER => (string)__('Manual'),
            \M2E\AmazonMcf\Model\Logger\Initiator::INITIATOR_EXTENSION => (string)__('Automatic'),
        ];

        $options = [];
        foreach ($initiators as $value => $label) {
            $options[] = [
                'label' => $label,
                'value' => $value,
            ];
        }

        return $options;
    }
}
