<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Provider\Amazon\Order;

class Repository
{
    /** @var \Ess\M2ePro\Model\AmazonMcf\Amazon\Provider\Order\Repository|Repository\BaseInterface */
    private $providerRepository;
    private array $cacheIsExistsWithMagentoOrder = [];

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \M2E\AmazonMcf\Helper\M2EPro $m2eproHelper
    ) {
        $providerIsAvailable = $m2eproHelper->isModuleEnabled()
            && class_exists(\Ess\M2ePro\Model\AmazonMcf\Amazon\Provider\Order\Repository::class);

        /** @psalm-suppress UndefinedClass */
        $this->providerRepository = $providerIsAvailable
            ? $objectManager->create(\Ess\M2ePro\Model\AmazonMcf\Amazon\Provider\Order\Repository::class)
            : new Repository\Stub();
    }

    public function isExistsWithMagentoOrder(int $magentoOrderId): bool
    {
        return $this->cacheIsExistsWithMagentoOrder[$magentoOrderId]
            ?? $this->cacheIsExistsWithMagentoOrder[$magentoOrderId] = $this->providerRepository
                ->isExistsWithMagentoOrder($magentoOrderId);
    }
}
