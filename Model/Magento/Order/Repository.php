<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Magento\Order;

class Repository
{
    private \Magento\Sales\Model\OrderRepository $magentoOrderRepository;

    public function __construct(
        \Magento\Sales\Model\OrderRepository $magentoOrderRepository
    ) {
        $this->magentoOrderRepository = $magentoOrderRepository;
    }

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(int $id): \Magento\Sales\Model\Order
    {
        /** @var \Magento\Sales\Model\Order */
        return $this->magentoOrderRepository->get($id);
    }

    public function find(int $id): ?\Magento\Sales\Model\Order
    {
        try {
            return $this->get($id);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return null;
        }
    }
}
