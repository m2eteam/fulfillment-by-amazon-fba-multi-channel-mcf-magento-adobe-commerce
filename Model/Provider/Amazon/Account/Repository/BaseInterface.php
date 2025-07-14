<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Provider\Amazon\Account\Repository;

interface BaseInterface
{
    public function isExists(string $merchantId): bool;

    public function find(string $merchantId): ?\M2E\AmazonMcf\Model\Provider\Amazon\Account\Item;

    /**
     * @return \M2E\AmazonMcf\Model\Provider\Amazon\Account\Item[]
     */
    public function getAll(): array;
}
