<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Order;

class PayService
{
    private Repository $orderRepository;
    private ChannelChecker $channelChecker;

    public function __construct(Repository $orderRepository, ChannelChecker $channelChecker)
    {
        $this->orderRepository = $orderRepository;
        $this->channelChecker = $channelChecker;
    }

    public function canPay(\M2E\AmazonMcf\Model\Order $order): bool
    {
        if ($order->isPaid()) {
            return false;
        }

        return $this->channelChecker->isAllowedChannel(
            $order->getChannel()
        );
    }

    public function pay(\M2E\AmazonMcf\Model\Order $order): void
    {
        if (!$this->canPay($order)) {
            throw new \LogicException('Order cannot be payed');
        }

        $order->pay();
        $this->orderRepository->save($order);
    }
}
