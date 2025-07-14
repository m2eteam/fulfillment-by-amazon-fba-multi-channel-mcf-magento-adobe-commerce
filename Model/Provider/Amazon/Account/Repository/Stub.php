<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Provider\Amazon\Account\Repository;

class Stub implements BaseInterface
{
    public function isExists(string $merchantId): bool
    {
        return false;
    }

    public function find(string $merchantId): ?\M2E\AmazonMcf\Model\Provider\Amazon\Account\Item
    {
        return null;
    }

    /**
     * @return \M2E\AmazonMcf\Model\Provider\Amazon\Account\Item[]
     */
    public function getAll(): array
    {
        return [];
    }
}
