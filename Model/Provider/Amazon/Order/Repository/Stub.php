<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Provider\Amazon\Order\Repository;

class Stub implements BaseInterface
{
    public function isExistsWithMagentoOrder(int $magentoOrderId): bool
    {
        return false;
    }
}
