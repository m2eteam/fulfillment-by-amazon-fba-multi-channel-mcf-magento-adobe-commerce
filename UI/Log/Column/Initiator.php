<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\UI\Log\Column;

class Initiator extends \Magento\Ui\Component\Listing\Columns\Column
{
    public function prepareDataSource(array $dataSource): array
    {
        if (empty($dataSource['data']['items'])) {
            return $dataSource;
        }

        $map = [
            \M2E\AmazonMcf\Model\Logger\Initiator::INITIATOR_UNKNOWN => 'Unknown',
            \M2E\AmazonMcf\Model\Logger\Initiator::INITIATOR_USER => 'Manual',
            \M2E\AmazonMcf\Model\Logger\Initiator::INITIATOR_EXTENSION => 'Automatic',
        ];

        foreach ($dataSource['data']['items'] as &$item) {
            $initiator = $map[(int)$item['initiator']] ?? 'Unknown';
            $item['initiator'] = __($initiator);
        }

        return $dataSource;
    }
}
