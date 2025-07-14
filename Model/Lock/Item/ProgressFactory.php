<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Lock\Item;

class ProgressFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(
        \M2E\AmazonMcf\Model\Lock\Item\Manager $lockItemManager,
        $progressNick
    ): Progress {
        return $this->objectManager->create(
            Progress::class,
            [
                'lockItemManager' => $lockItemManager,
                'progressNick' => $progressNick,
            ],
        );
    }
}
