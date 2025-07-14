<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Magento\Order\Item;

class Repository
{
    private \Magento\Sales\Model\Order\ItemRepository $magentoOrderItemsRepository;

    public function __construct(
        \Magento\Sales\Model\Order\ItemRepository $magentoOrderItemsRepository
    ) {
        $this->magentoOrderItemsRepository = $magentoOrderItemsRepository;
    }

    public function get(int $id): \Magento\Sales\Model\Order\Item
    {
        /** @var \Magento\Sales\Model\Order\Item */
        return $this->magentoOrderItemsRepository->get($id);
    }
}
