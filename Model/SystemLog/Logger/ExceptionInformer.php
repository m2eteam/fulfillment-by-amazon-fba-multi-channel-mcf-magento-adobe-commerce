<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\SystemLog\Logger;

class ExceptionInformer
{
    public function getExceptionInfo(\Throwable $exception): array
    {
        return [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'code' => $exception->getCode(),
            'trace' => $this->getTraceInfo($exception->getTrace()),
        ];
    }

    private function getTraceInfo(array $backtrace): array
    {
        $trace = [];
        foreach ($backtrace as $item) {
            $trace[] = [
                'class' => $item['class'] ?? '',
                'func' => $item['function'] ?? '',
                'file' => $item['file'] ?? '',
                'line' => $item['line'] ?? '',
            ];
        }

        return $trace;
    }
}
