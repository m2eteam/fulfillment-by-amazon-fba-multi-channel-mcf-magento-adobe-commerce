<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model;

use M2E\AmazonMcf\Model\ResourceModel\Product as ProductResource;

class Product extends \Magento\Framework\Model\AbstractModel
{
    private Account\Repository $accountRepository;
    private ?Account $account = null;

    public function __construct(
        \M2E\AmazonMcf\Model\Account\Repository $accountRepository,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ?\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        ?\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->accountRepository = $accountRepository;
    }

    protected function _construct(): void
    {
        parent::_construct();
        $this->_init(ProductResource::class);
    }

    public function init(
        int $accountId,
        string $channelSku,
        int $magentoProductId,
        string $magentoProductSku,
        int $qty
    ): self {
        $this->setData(ProductResource::COLUMN_ACCOUNT_ID, $accountId);
        $this->setData(ProductResource::COLUMN_CHANNEL_SKU, $channelSku);
        $this->setData(ProductResource::COLUMN_MAGENTO_PRODUCT_ID, $magentoProductId);
        $this->setMagentoProductSku($magentoProductSku);
        $this->setQty($qty);
        $this->enable();

        return $this;
    }

    // ---------------------------------------

    public function getId(): ?int
    {
        $id = $this->getDataByKey(ProductResource::COLUMN_ID);
        if ($id === null) {
            return null;
        }

        return (int)$id;
    }

    public function getAccountId(): int
    {
        return (int)$this->getDataByKey(ProductResource::COLUMN_ACCOUNT_ID);
    }

    public function getAccount(): Account
    {
        if ($this->account !== null) {
            return $this->account;
        }

        return $this->account = $this->accountRepository->get($this->getAccountId());
    }

    // ---------------------------------------

    public function getChannelSku(): string
    {
        return $this->getDataByKey(ProductResource::COLUMN_CHANNEL_SKU);
    }

    public function getMagentoProductId(): int
    {
        return (int)$this->getDataByKey(ProductResource::COLUMN_MAGENTO_PRODUCT_ID);
    }

    public function getMagentoProductSku(): string
    {
        return $this->getDataByKey(ProductResource::COLUMN_MAGENTO_PRODUCT_SKU);
    }

    public function setMagentoProductSku(string $magentoProductSku): self
    {
        $this->setData(ProductResource::COLUMN_MAGENTO_PRODUCT_SKU, $magentoProductSku);

        return $this;
    }

    // ---------------------------------------

    public function setQty(int $qty): self
    {
        $this->setData(ProductResource::COLUMN_QTY, $qty);
        $this->setData(
            ProductResource::COLUMN_QTY_LAST_UPDATE_DATE,
            \M2E\Core\Helper\Date::createCurrentGmt()->format('Y-m-d H:i:s')
        );

        return $this;
    }

    public function incrementQty(int $qty): self
    {
        $newQty = $qty + $this->getQty();
        $this->setData(ProductResource::COLUMN_QTY, $newQty);

        return $this;
    }

    public function decrementQty(int $qty): self
    {
        $newQty = max($this->getQty() - $qty, 0);
        $this->setData(ProductResource::COLUMN_QTY, $newQty);

        return $this;
    }

    public function getQty(): int
    {
        return (int)$this->getDataByKey(ProductResource::COLUMN_QTY);
    }

    public function getQtyLastUpdateDate(): \DateTime
    {
        if ($this->getDataByKey(ProductResource::COLUMN_QTY_LAST_UPDATE_DATE) === null) {
            throw new \LogicException('Product Qty Last Update Date is not exists');
        }

        return \M2E\Core\Helper\Date::createDateGmt(
            $this->getDataByKey(ProductResource::COLUMN_QTY_LAST_UPDATE_DATE)
        );
    }

    // ---------------------------------------

    public function findAsin(): ?string
    {
        return $this->getDataByKey(ProductResource::COLUMN_ASIN);
    }

    public function setAsin(string $asin): self
    {
        $this->setData(ProductResource::COLUMN_ASIN, $asin);

        return $this;
    }

    // ---------------------------------------

    public function isEnabled(): bool
    {
        return (bool)$this->getDataByKey(ProductResource::COLUMN_IS_ENABLED);
    }

    public function enable(): self
    {
        $this->setData(ProductResource::COLUMN_IS_ENABLED, 1);

        return $this;
    }

    public function disable(): self
    {
        $this->setData(ProductResource::COLUMN_IS_ENABLED, 0);

        return $this;
    }
}
