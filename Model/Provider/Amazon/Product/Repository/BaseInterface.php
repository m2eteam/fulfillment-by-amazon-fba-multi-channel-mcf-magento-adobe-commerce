<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Provider\Amazon\Product\Repository;

interface BaseInterface
{
    public function getItemCollection(string $merchantId): \M2E\AmazonMcf\Model\Provider\Amazon\Product\ItemCollection;
}
