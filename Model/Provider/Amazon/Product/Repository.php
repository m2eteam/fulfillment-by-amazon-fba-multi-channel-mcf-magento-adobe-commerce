<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Provider\Amazon\Product;

class Repository
{
    /** @var \Ess\M2ePro\Model\AmazonMcf\Amazon\Provider\Product\Repository|Repository\BaseInterface */
    private $providerRepository;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \M2E\AmazonMcf\Helper\M2EPro $m2eproHelper
    ) {
        /** @psalm-suppress UndefinedClass */
        $this->providerRepository = $m2eproHelper->isModuleEnabled()
            ? $objectManager->create(\Ess\M2ePro\Model\AmazonMcf\Amazon\Provider\Product\Repository::class)
            : new Repository\Stub();
    }

    public function getItemCollection(string $merchantId): \M2E\AmazonMcf\Model\Provider\Amazon\Product\ItemCollection
    {
        return $this->providerRepository->getItemCollection($merchantId);
    }
}
