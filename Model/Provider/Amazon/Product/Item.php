<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Provider\Amazon\Product;

class Item
{
    private string $merchantId;
    private string $channelSku;
    private int $magentoProductId;
    private string $magentoProductSku;
    private int $qty;
    private ?string $asin = null;

    public function __construct(
        string $merchantId,
        string $channelSku,
        int $magentoProductId,
        string $magentoProductSku,
        int $qty
    ) {
        $this->merchantId = $merchantId;
        $this->channelSku = $channelSku;
        $this->magentoProductId = $magentoProductId;
        $this->magentoProductSku = $magentoProductSku;
        $this->qty = $qty;
    }

    public function getMerchantId(): string
    {
        return $this->merchantId;
    }

    public function getChannelSku(): string
    {
        return $this->channelSku;
    }

    public function getMagentoProductId(): int
    {
        return $this->magentoProductId;
    }

    public function getMagentoProductSku(): string
    {
        return $this->magentoProductSku;
    }

    public function getQty(): int
    {
        return $this->qty;
    }

    // ---------------------------------------

    public function isExistsAsin(): bool
    {
        return $this->asin !== null;
    }

    public function getAsin(): ?string
    {
        return $this->asin;
    }

    public function setAsin(string $asin): self
    {
        $this->asin = $asin;

        return $this;
    }

    // ---------------------------------------
}
