<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\UI\Order\Column;

class Status extends \Magento\Ui\Component\Listing\Columns\Column
{
    public function prepareDataSource(array $dataSource): array
    {
        if (empty($dataSource['data']['items'])) {
            return $dataSource;
        }

        $map = [
            \M2E\AmazonMcf\Model\Order::STATUS_PENDING => ['text' => 'Pending', 'color' => '#6f6f6f'],
            \M2E\AmazonMcf\Model\Order::STATUS_SKIPPED => ['text' => 'Unprocessed', 'color' => '#fa4d56'],
            \M2E\AmazonMcf\Model\Order::STATUS_WAIT_CREATED_PACKAGE => [
                'text' => 'Created Package',
                'color' => '#ff832b',
            ],
            \M2E\AmazonMcf\Model\Order::STATUS_WAIT_SHIP => ['text' => 'Wait Ship', 'color' => '#ff832b'],
            \M2E\AmazonMcf\Model\Order::STATUS_SHIPPED => ['text' => 'Shipped', 'color' => '#4589ff'],
            \M2E\AmazonMcf\Model\Order::STATUS_COMPLETE => ['text' => 'Complete', 'color' => '#24A148'],
        ];

        foreach ($dataSource['data']['items'] as &$item) {
            $status = $map[(int)$item['status']] ?? ['text' => '', 'color' => ''];
            $item['status'] = sprintf(
                '<span style="color: %s; font-weight: bold;">%s</span>',
                $status['color'],
                __($status['text'])
            );
        }

        return $dataSource;
    }
}
