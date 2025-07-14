<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Lock;

class TransactionalFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(): Transactional
    {
        return $this->objectManager->create(Transactional::class);
    }
}
