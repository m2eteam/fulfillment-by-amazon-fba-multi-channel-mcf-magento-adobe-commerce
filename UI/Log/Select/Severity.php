<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\UI\Log\Select;

class Severity implements \Magento\Framework\Data\OptionSourceInterface
{
    public function toOptionArray(): array
    {
        $severities = [
            \M2E\AmazonMcf\Model\SystemLog\Logger::SEVERITY_INFO => (string)__('Info'),
            \M2E\AmazonMcf\Model\SystemLog\Logger::SEVERITY_NOTICE => (string)__('Notice'),
            \M2E\AmazonMcf\Model\SystemLog\Logger::SEVERITY_WARNING => (string)__('Warning'),
            \M2E\AmazonMcf\Model\SystemLog\Logger::SEVERITY_ERROR => (string)__('Error'),
            \M2E\AmazonMcf\Model\SystemLog\Logger::SEVERITY_CRITICAL => (string)__('Critical Error'),
        ];

        $options = [];
        foreach ($severities as $value => $label) {
            $options[] = [
                'label' => $label,
                'value' => $value,
            ];
        }

        return $options;
    }
}
