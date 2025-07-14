<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\UI\Order\Select;

class Status implements \Magento\Framework\Data\OptionSourceInterface
{
    public function toOptionArray(): array
    {
        $status = [
            \M2E\AmazonMcf\Model\Order::STATUS_PENDING => __('Pending'),
            \M2E\AmazonMcf\Model\Order::STATUS_SKIPPED => __('Unprocessed'),
            \M2E\AmazonMcf\Model\Order::STATUS_WAIT_CREATED_PACKAGE => __('Wait Created Package'),
            \M2E\AmazonMcf\Model\Order::STATUS_WAIT_SHIP => __('Wait Ship'),
            \M2E\AmazonMcf\Model\Order::STATUS_SHIPPED => __('Shipped'),
            \M2E\AmazonMcf\Model\Order::STATUS_COMPLETE => __('Complete'),
        ];

        $options = [];
        foreach ($status as $value => $label) {
            $options[] = [
                'label' => $label,
                'value' => $value,
            ];
        }

        return $options;
    }
}
