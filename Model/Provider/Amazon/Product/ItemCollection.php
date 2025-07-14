<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Provider\Amazon\Product;

class ItemCollection
{
    /** @var list<string, Item>  */
    private array $itemsByChannelSku = [];

    public function add(Item $item): self
    {
        $this->itemsByChannelSku[$item->getChannelSku()] = $item;

        return $this;
    }

    /**
     * @return Item[]
     */
    public function getItems(): array
    {
        return array_values($this->itemsByChannelSku);
    }

    public function findItemByChannelSku(string $channelSku): ?Item
    {
        return $this->itemsByChannelSku[$channelSku] ?? null;
    }

    public function isEmpty(): bool
    {
        return empty($this->itemsByChannelSku);
    }
}
