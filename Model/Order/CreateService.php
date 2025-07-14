<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Order;

class CreateService
{
    private Repository $orderRepository;
    private \M2E\AmazonMcf\Model\Provider\Amazon\Order\Repository $amazonOrderRepository;
    private \M2E\AmazonMcf\Model\OrderFactory $orderFactory;
    private ChannelChecker $channelChecker;
    private Log\Logger $orderLogger;
    private \M2E\AmazonMcf\Model\Order\PayService $payService;

    private array $cacheCanCreateForMagento = [];

    public function __construct(
        Repository $orderRepository,
        \M2E\AmazonMcf\Model\Provider\Amazon\Order\Repository $amazonOrderRepository,
        \M2E\AmazonMcf\Model\OrderFactory $orderFactory,
        ChannelChecker $channelChecker,
        \M2E\AmazonMcf\Model\Order\Log\Logger $orderLogger,
        \M2E\AmazonMcf\Model\Order\PayService $payService
    ) {
        $this->orderRepository = $orderRepository;
        $this->amazonOrderRepository = $amazonOrderRepository;
        $this->orderFactory = $orderFactory;
        $this->channelChecker = $channelChecker;
        $this->orderLogger = $orderLogger;
        $this->payService = $payService;
    }

    public function canCreate(CreateService\OrderInput $input): bool
    {
        return $input->channel !== \M2E\AmazonMcf\Model\Order::CHANNEL_MAGENTO
            && \M2E\AmazonMcf\Model\Account::isFamousRegion($input->region)
            && !$this->amazonOrderRepository->isExistsWithMagentoOrder($input->magentoOrderId);
    }

    public function create(CreateService\OrderInput $input): void
    {
        if (!$this->canCreate($input)) {
            throw new \LogicException('Order cannot be created.');
        }

        $order = $this->orderRepository->findByMagentoOrderId($input->magentoOrderId);
        if ($order !== null) {
            if (
                $order->isMagentoChannel()
                && $order->isPendingStatus()
                && $order->getRegion() === $input->region
            ) {
                $this->updateOrder($order, $input);
            }

            return;
        }

        if ($this->channelChecker->isAllowedChannel($input->channel)) {
            $this->createOrder($input);
        }
    }

    private function updateOrder(\M2E\AmazonMcf\Model\Order $order, CreateService\OrderInput $input): void
    {
        if ($this->channelChecker->isDisallowedChannel($input->channel)) {
            $order->setStatusSkipped();
            $this->orderRepository->save($order);

            return;
        }

        $order->setChannel($input->channel)
              ->setChannelOrderId($input->channelOrderId);

        if ($input->channelExternalOrderId) {
            $order->setChannelExternalOrderId($input->channelExternalOrderId);
        }

        if ($input->channelPurchaseDate) {
            $order->setChannelPurchaseDate($input->channelPurchaseDate);
        }

        $this->orderRepository->save($order);
    }

    private function createOrder(CreateService\OrderInput $input): void
    {
        $order = $this->orderFactory->create(
            $input->channel,
            $input->channelOrderId,
            $input->magentoOrderId,
            $input->magentoOrderIncrementId,
            $input->region,
            $input->channelExternalOrderId,
            $input->channelPurchaseDate
        );

        $this->orderRepository->create($order);
    }

    // ---------------------------------------

    public function canCreateForMagentoChannel(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Sales\Model\Order $magentoOrder
    ): bool {
        if ($quote->isVirtual()) {
            return false;
        }

        $magentoOrderId = (int)$magentoOrder->getId();

        return $this->cacheCanCreateForMagento[$magentoOrderId]
            ?? $this->cacheCanCreateForMagento[$magentoOrderId] =
                $this->channelChecker->isAllowedChannel(\M2E\AmazonMcf\Model\Order::CHANNEL_MAGENTO)
                && !$this->orderRepository->isExistsWithMagentoOrderId($magentoOrderId)
                && !$this->amazonOrderRepository->isExistsWithMagentoOrder($magentoOrderId);
    }

    public function createForMagentoChannel(
        \Magento\Sales\Model\Order $magentoOrder,
        \Magento\Quote\Model\Quote $quote
    ): void {
        if (!$this->canCreateForMagentoChannel($quote, $magentoOrder)) {
            throw new \LogicException('Cannot create order for Magento Channel.');
        }

        $countryId = $quote->getShippingAddress()
                           ->getCountryId();

        $region = \M2E\AmazonMcf\Model\Magento\RegionMapper::getRegionByCountryId($countryId);
        $purchaseDate = \M2E\Core\Helper\Date::createDateGmt(
            $magentoOrder->getCreatedAt()
        );
        $order = $this->orderFactory->createWithMagentoChannel(
            (int)$magentoOrder->getId(),
            $magentoOrder->getIncrementId(),
            $region,
            $purchaseDate
        );

        $this->orderRepository->create($order);
        if (
            $this->payService->canPay($order)
            && $magentoOrder->getState() === \Magento\Sales\Model\Order::STATE_PROCESSING
        ) {
            $this->payService->pay($order);
        }

        $this->addLogForRegionMapping($region, $countryId, $order->getId());
    }

    private function addLogForRegionMapping(
        string $region,
        string $countryId,
        int $orderId
    ): void {
        $regionTitleMap = [
            \M2E\AmazonMcf\Model\Account::REGION_AMERICA => __('America'),
            \M2E\AmazonMcf\Model\Account::REGION_EUROPE => __('Europe'),
            \M2E\AmazonMcf\Model\Account::REGION_ASIA_PACIFIC => __('Asia Pacific'),
        ];

        $this->orderLogger->notice(
            (string)__(
                'Order was assigned to Amazon region [%region]'
                . ' based on the country ID [%country_id] from the shipping address.',
                [
                    'country_id' => $countryId,
                    'region' => (string)$regionTitleMap[$region],
                ]
            ),
            $orderId
        );
    }
}
