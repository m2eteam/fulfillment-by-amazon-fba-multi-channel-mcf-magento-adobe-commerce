<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Product;

use M2E\AmazonMcf\Model\ResourceModel\Product\Log as LogResource;

class Log extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct(): void
    {
        parent::_construct();
        $this->_init(\M2E\AmazonMcf\Model\ResourceModel\Product\Log::class);
    }

    public function init(
        int $productId,
        string $message,
        int $severity,
        int $initiator
    ): self {
        $this->setData(LogResource::COLUMN_PRODUCT_ID, $productId);
        $this->setData(LogResource::COLUMN_MESSAGE, $this->trimMessage($message));
        $this->setData(LogResource::COLUMN_SEVERITY, $severity);
        $this->setData(LogResource::COLUMN_INITIATOR, $initiator);

        return $this;
    }

    private function trimMessage(string $message): string
    {
        if (strlen($message) <= 255) {
            return $message;
        }

        return substr($message, 0, 252) . '...';
    }

    public function getContext(): array
    {
        $value = $this->getData(LogResource::COLUMN_CONTEXT);
        if (empty($value)) {
            return [];
        }

        return (array)json_decode($value, true);
    }

    public function setContext(array $context): self
    {
        $this->setData(
            LogResource::COLUMN_CONTEXT,
            json_encode($context)
        );

        return $this;
    }
}
