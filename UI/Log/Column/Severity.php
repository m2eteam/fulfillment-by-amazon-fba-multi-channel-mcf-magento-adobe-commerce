<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\UI\Log\Column;

use M2E\AmazonMcf\Model\SystemLog\Logger;

class Severity extends \Magento\Ui\Component\Listing\Columns\Column
{
    public function prepareDataSource(array $dataSource): array
    {
        if (empty($dataSource['data']['items'])) {
            return $dataSource;
        }

        $map = [
            Logger::SEVERITY_INFO => ['text' => 'Info', 'color' => 'gray',],
            Logger::SEVERITY_NOTICE => ['text' => 'Notice', 'color' => 'gray',],
            Logger::SEVERITY_WARNING => ['text' => 'Warning', 'color' => 'orange',],
            Logger::SEVERITY_ERROR => ['text' => 'Error', 'color' => 'red',],
            Logger::SEVERITY_CRITICAL => ['text' => 'Critical Error', 'color' => 'red',],
        ];

        foreach ($dataSource['data']['items'] as &$item) {
            $severity = $map[(int)$item['severity']] ?? ['text' => '', 'color' => '',];
            $item['severity'] = sprintf(
                '<span style="color: %s; font-weight: bold;">%s</span>',
                $severity['color'],
                __($severity['text'])
            );
        }

        return $dataSource;
    }
}
