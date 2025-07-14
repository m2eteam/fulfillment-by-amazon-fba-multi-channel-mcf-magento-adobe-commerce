<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model;

use M2E\AmazonMcf\Model\ResourceModel\Order as OrderResource;

class Order extends \Magento\Framework\Model\AbstractModel
{
    public const STATUS_PENDING = 1;
    public const STATUS_SKIPPED = 2;
    public const STATUS_WAIT_CREATED_PACKAGE = 3;
    public const STATUS_WAIT_SHIP = 4;
    public const STATUS_SHIPPED = 5;
    public const STATUS_COMPLETE = 6;

    public const CHANNEL_EBAY = 'ebay';
    public const CHANNEL_WALMART = 'walmart';
    public const CHANNEL_MAGENTO = 'magento';
    public const CHANNEL_TIKTOK_SHOP = 'tts';
    public const CHANNEL_KAUFLAND = 'kaufland';
    public const CHANNEL_ONBUY = 'onbuy';
    public const CHANNEL_OTTO = 'otto';
    public const CHANNEL_TEMU = 'temu';

    private Order\Item\Repository $itemRepository;
    private Account\Repository $accountRepository;
    private \M2E\AmazonMcf\Model\Magento\Order\Repository $magentoOrderRepository;
    private ?\Magento\Sales\Model\Order $magentoOrder = null;
    private ?Account $account = null;
    /** @var \M2E\AmazonMcf\Model\Order\Item[]|null */
    private ?array $items = null;

    public function __construct(
        \M2E\AmazonMcf\Model\Order\Item\Repository $itemRepository,
        \M2E\AmazonMcf\Model\Account\Repository $accountRepository,
        \M2E\AmazonMcf\Model\Magento\Order\Repository $magentoOrderRepository,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ?\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        ?\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->itemRepository = $itemRepository;
        $this->accountRepository = $accountRepository;
        $this->magentoOrderRepository = $magentoOrderRepository;
    }

    protected function _construct(): void
    {
        parent::_construct();
        $this->_init(OrderResource::class);
    }

    public function init(
        string $channel,
        int $magentoOrderId,
        string $magentoOrderIncrementId,
        string $region
    ): self {
        $this->setChannel($channel);
        $this->setStatus(self::STATUS_PENDING);
        $this->setData(OrderResource::COLUMN_IS_PAID, false);
        $this->setData(OrderResource::COLUMN_MAGENTO_ORDER_ID, $magentoOrderId);
        $this->setData(OrderResource::COLUMN_MAGENTO_ORDER_INCREMENT_ID, $magentoOrderIncrementId);
        $this->setData(OrderResource::COLUMN_REGION, $region);

        return $this;
    }

    public function getId(): ?int
    {
        $id = $this->getDataByKey(OrderResource::COLUMN_ID);
        if ($id === null) {
            return null;
        }

        return (int)$id;
    }

    // ----------------------------------------

    public function isMagentoChannel(): bool
    {
        return $this->getChannel() === self::CHANNEL_MAGENTO;
    }

    public function getChannel(): string
    {
        return (string)$this->getData(OrderResource::COLUMN_CHANNEL);
    }

    public function setChannel(string $channel): self
    {
        $this->setData(OrderResource::COLUMN_CHANNEL, $channel);

        return $this;
    }

    // ----------------------------------------

    public function isExistsChannelOrderId(): bool
    {
        return $this->getDataByKey(OrderResource::COLUMN_CHANNEL_ORDER_ID) !== null;
    }

    public function getChannelOrderId(): int
    {
        if (!$this->isExistsChannelOrderId()) {
            throw new \RuntimeException('Channel order id not found');
        }

        return (int)$this->getData(OrderResource::COLUMN_CHANNEL_ORDER_ID);
    }

    public function setChannelOrderId(int $channelOrderId): self
    {
        $this->setData(OrderResource::COLUMN_CHANNEL_ORDER_ID, $channelOrderId);

        return $this;
    }

    // ----------------------------------------

    public function isExistsChannelExternalOrderId(): bool
    {
        return $this->getDataByKey(OrderResource::COLUMN_CHANNEL_EXTERNAL_ORDER_ID) !== null;
    }

    public function getChannelExternalOrderId(): string
    {
        if (!$this->isExistsChannelExternalOrderId()) {
            throw new \RuntimeException('Channel external order id not found');
        }

        return $this->getDataByKey(OrderResource::COLUMN_CHANNEL_EXTERNAL_ORDER_ID);
    }

    public function setChannelExternalOrderId(string $externalOrderId): self
    {
        $this->setData(OrderResource::COLUMN_CHANNEL_EXTERNAL_ORDER_ID, $externalOrderId);

        return $this;
    }

    // ----------------------------------------

    public function getMagentoOrderId(): int
    {
        return (int)$this->getData(OrderResource::COLUMN_MAGENTO_ORDER_ID);
    }

    public function getMagentoOrder(): \Magento\Sales\Model\Order
    {
        if ($this->magentoOrder !== null) {
            return $this->magentoOrder;
        }

        $order = $this->magentoOrderRepository->find($this->getMagentoOrderId());

        if ($order === null) {
            throw new \LogicException(
                sprintf(
                    'Magento order not found by id - %d. MCF Order id - %d.',
                    $this->getMagentoOrderId(),
                    $this->getId()
                )
            );
        }

        return $this->magentoOrder = $order;
    }

    public function getMagentoOrderIncrementId(): string
    {
        return $this->getData(OrderResource::COLUMN_MAGENTO_ORDER_INCREMENT_ID);
    }

    // ----------------------------------------

    public function getRegion(): string
    {
        return $this->getData(OrderResource::COLUMN_REGION);
    }

    // ---------------------------------------

    public function isExistsAccountId(): bool
    {
        return $this->getData(OrderResource::COLUMN_ACCOUNT_ID) !== null;
    }

    public function getAccountId(): int
    {
        $accountId = $this->getData(OrderResource::COLUMN_ACCOUNT_ID);
        if ($accountId === null) {
            throw new \LogicException('Account id is not set');
        }

        return (int)$accountId;
    }

    public function getAccount(): Account
    {
        if ($this->account !== null) {
            return $this->account;
        }

        return $this->account = $this->accountRepository->get($this->getAccountId());
    }

    public function setAccountId(int $accountId): self
    {
        $this->setData(OrderResource::COLUMN_ACCOUNT_ID, $accountId);

        return $this;
    }

    // ---------------------------------------

    public function isPendingStatus(): bool
    {
        return $this->getStatus() === self::STATUS_PENDING;
    }

    public function setStatusSkipped(): self
    {
        $this->setStatus(self::STATUS_SKIPPED);

        return $this;
    }

    public function isWaitCreatedPackageStatus(): bool
    {
        return $this->getStatus() === self::STATUS_WAIT_CREATED_PACKAGE;
    }

    public function setStatusWaitCreatedPackage(): void
    {
        $this->setStatus(self::STATUS_WAIT_CREATED_PACKAGE);
    }

    public function isWaitShipStatus(): bool
    {
        return $this->getStatus() === self::STATUS_WAIT_SHIP;
    }

    public function setStatusWaitShip(): self
    {
        $this->setStatus(self::STATUS_WAIT_SHIP);

        return $this;
    }

    public function isStatusShipped(): bool
    {
        return $this->getStatus() === self::STATUS_SHIPPED;
    }

    public function setStatusShipped(): self
    {
        $this->setStatus(self::STATUS_SHIPPED);

        return $this;
    }

    public function setStatusComplete(): self
    {
        $this->setStatus(self::STATUS_COMPLETE);

        return $this;
    }

    private function getStatus(): int
    {
        return (int)$this->getData(OrderResource::COLUMN_STATUS);
    }

    private function setStatus(int $status): void
    {
        $this->setData(OrderResource::COLUMN_STATUS, $status);
        $this->stopWaitingOfNextStatusProcess();
    }

    // ---------------------------------------

    public function pay(): self
    {
        $this->setData(OrderResource::COLUMN_IS_PAID, true);

        return $this;
    }

    public function isPaid(): bool
    {
        return (bool)$this->getDataByKey(OrderResource::COLUMN_IS_PAID);
    }

    // ---------------------------------------

    public function getSellerFulfillmentId(): string
    {
        if (!$this->isExistsSellerFulfillmentId()) {
            throw new \LogicException('Seller Fulfillment ID is not exists');
        }

        return $this->getDataByKey(OrderResource::COLUMN_SELLER_FULFILLMENT_ID);
    }

    public function isExistsSellerFulfillmentId(): bool
    {
        return $this->getDataByKey(OrderResource::COLUMN_SELLER_FULFILLMENT_ID) !== null;
    }

    public function setSellerFulfillmentId(string $sellerFulfilmentId): self
    {
        $this->setData(OrderResource::COLUMN_SELLER_FULFILLMENT_ID, $sellerFulfilmentId);

        return $this;
    }

    // ---------------------------------------

    public function setShippingDate(\DateTime $dateTime): self
    {
        $dateTime->setTimezone(
            new \DateTimeZone(\M2E\Core\Helper\Date::getTimezone()->getDefaultTimezone())
        );
        $this->setData(OrderResource::COLUMN_SHIPPING_DATE, $dateTime->format('Y-m-d H:i:s'));

        return $this;
    }

    // ---------------------------------------

    public function getQtyReservationDate(): \DateTime
    {
        if (!$this->isExistsQtyReservationDate()) {
            throw new \LogicException('Order Qty Reservation Date is not exists');
        }

        return \M2E\Core\Helper\Date::createDateGmt(
            $this->getDataByKey(OrderResource::COLUMN_QTY_RESERVATION_DATE)
        );
    }

    public function isExistsQtyReservationDate(): bool
    {
        return $this->getDataByKey(OrderResource::COLUMN_QTY_RESERVATION_DATE) !== null;
    }

    public function setQtyReservationDate(\DateTime $dateTime): self
    {
        $dateTime->setTimezone(
            new \DateTimeZone(\M2E\Core\Helper\Date::getTimezone()->getDefaultTimezone())
        );
        $this->setData(OrderResource::COLUMN_QTY_RESERVATION_DATE, $dateTime->format('Y-m-d H:i:s'));

        return $this;
    }

    public function clearQtyReservationDate(): self
    {
        $this->setData(OrderResource::COLUMN_QTY_RESERVATION_DATE, null);

        return $this;
    }

    // ---------------------------------------

    public function isExistsChannelPurchaseDate(): bool
    {
        return $this->getDataByKey(OrderResource::COLUMN_CHANNEL_PURCHASE_DATE) !== null;
    }

    public function getChannelPurchaseDate(): \DateTime
    {
        if (!$this->isExistsChannelPurchaseDate()) {
            throw new \RuntimeException('Channel Purchase Date is not exists');
        }

        return \M2E\Core\Helper\Date::createDateGmt(
            $this->getDataByKey(OrderResource::COLUMN_CHANNEL_PURCHASE_DATE)
        );
    }

    public function setChannelPurchaseDate(\DateTime $dateTime): self
    {
        $dateTime->setTimezone(
            new \DateTimeZone(\M2E\Core\Helper\Date::getTimezone()->getDefaultTimezone())
        );
        $this->setData(OrderResource::COLUMN_CHANNEL_PURCHASE_DATE, $dateTime->format('Y-m-d H:i:s'));

        return $this;
    }

    // ---------------------------------------

    public function waitNextStatusProcess(): void
    {
        $attemptCount = $this->getStatusProcessAttemptCount();
        $this->setData(
            OrderResource::COLUMN_STATUS_PROCESS_ATTEMPT_COUNT,
            ++$attemptCount
        );

        $this->setData(
            OrderResource::COLUMN_STATUS_PROCESS_ATTEMPT_DATE,
            \M2E\Core\Helper\Date::createCurrentGmt()->format('Y-m-d H:i:s')
        );
    }

    public function stopWaitingOfNextStatusProcess(): void
    {
        $this->clearProcessAttemptCount();
        $this->setData(OrderResource::COLUMN_STATUS_PROCESS_ATTEMPT_DATE, null);
    }

    public function clearProcessAttemptCount(): void
    {
        $this->setData(OrderResource::COLUMN_STATUS_PROCESS_ATTEMPT_COUNT, 0);
    }

    public function getStatusProcessAttemptCount(): int
    {
        $count = $this->getData(OrderResource::COLUMN_STATUS_PROCESS_ATTEMPT_COUNT);
        if ($count === null) {
            return 0;
        }

        return (int)$count;
    }

    // ---------------------------------------

    /**
     * @return \M2E\AmazonMcf\Model\Order\Item[]
     */
    public function getItems(): array
    {
        if ($this->items !== null) {
            return $this->items;
        }

        return $this->items = $this->itemRepository->retrieveByOrderId($this->getId());
    }
}
