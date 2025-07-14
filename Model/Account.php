<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model;

use M2E\AmazonMcf\Model\ResourceModel\Account as AccountResource;

class Account extends \Magento\Framework\Model\AbstractModel
{
    public const REGION_EUROPE = 'europe';
    public const REGION_AMERICA = 'america';
    public const REGION_ASIA_PACIFIC = 'asia-pacific';

    private static array $allRegions = [
        \M2E\AmazonMcf\Model\Account::REGION_EUROPE,
        \M2E\AmazonMcf\Model\Account::REGION_AMERICA,
        \M2E\AmazonMcf\Model\Account::REGION_ASIA_PACIFIC,
    ];

    protected function _construct(): void
    {
        parent::_construct();
        $this->_init(AccountResource::class);
    }

    public function init(string $merchantId, string $region): self
    {
        $this->setData(AccountResource::COLUMN_MERCHANT_ID, $merchantId);
        $this->setData(AccountResource::COLUMN_REGION, $region);
        $this->enable();

        return $this;
    }

    public function getId(): ?int
    {
        $id = $this->getDataByKey(AccountResource::COLUMN_ID);
        if ($id === null) {
            return null;
        }

        return (int)$id;
    }

    public function getMerchantId(): string
    {
        return $this->getDataByKey(AccountResource::COLUMN_MERCHANT_ID);
    }

    public function getRegion(): string
    {
        return $this->getDataByKey(AccountResource::COLUMN_REGION);
    }

    public function enable(): void
    {
        $this->setData(AccountResource::COLUMN_IS_ENABLED, 1);
    }

    public function disable(): void
    {
        $this->setData(AccountResource::COLUMN_IS_ENABLED, 0);
    }

    public function isEnabled(): bool
    {
        return (bool)$this->getDataByKey(AccountResource::COLUMN_IS_ENABLED);
    }

    public static function isFamousRegion(string $region): bool
    {
        return in_array($region, self::$allRegions);
    }
}
