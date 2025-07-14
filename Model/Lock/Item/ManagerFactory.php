<?php

namespace M2E\AmazonMcf\Model\Lock\Item;

class ManagerFactory
{
    /** @var \Magento\Framework\ObjectManagerInterface */
    private $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(string $nick): \M2E\AmazonMcf\Model\Lock\Item\Manager
    {
        return $this->objectManager->create(
            \M2E\AmazonMcf\Model\Lock\Item\Manager::class,
            [
                'nick' => $nick,
            ]
        );
    }
}
