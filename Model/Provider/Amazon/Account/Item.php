<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Provider\Amazon\Account;

class Item
{
    private string $region;
    private string $merchantId;
    private bool $isEnabled;

    public static function createForAmericaRegion(string $merchantId, bool $isEnabled): self
    {
        return new self(
            \M2E\AmazonMcf\Model\Account::REGION_AMERICA,
            $merchantId,
            $isEnabled
        );
    }

    public static function createForEuropeRegion(string $merchantId, bool $isEnabled): self
    {
        return new self(
            \M2E\AmazonMcf\Model\Account::REGION_EUROPE,
            $merchantId,
            $isEnabled
        );
    }

    public static function createForAsiaPacificRegion(string $merchantId, bool $isEnabled): self
    {
        return new self(
            \M2E\AmazonMcf\Model\Account::REGION_ASIA_PACIFIC,
            $merchantId,
            $isEnabled
        );
    }

    private function __construct(string $region, string $merchantId, bool $isEnabled)
    {
        $this->region = $region;
        $this->merchantId = $merchantId;
        $this->isEnabled = $isEnabled;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function getMerchantId(): string
    {
        return $this->merchantId;
    }

    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }
}
