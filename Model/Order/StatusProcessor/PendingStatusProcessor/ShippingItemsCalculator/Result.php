<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Order\StatusProcessor\PendingStatusProcessor\ShippingItemsCalculator;

class Result
{
    /** @var Item[] */
    private array $items = [];
    private ?string $message = null;

    /**
     * @return Item[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param Item[] $items
     */
    public function setItems(array $items): self
    {
        $this->items = $items;

        return $this;
    }

    public function isExistsMessage(): bool
    {
        return $this->message !== null;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }
}
