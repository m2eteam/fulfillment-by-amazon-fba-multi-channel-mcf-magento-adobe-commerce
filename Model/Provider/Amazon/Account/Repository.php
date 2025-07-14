<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Provider\Amazon\Account;

class Repository
{
    /** @var \Ess\M2ePro\Model\AmazonMcf\Amazon\Provider\Account\Repository|Repository\BaseInterface */
    private $providerRepository;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \M2E\AmazonMcf\Helper\M2EPro $m2eproHelper
    ) {
        /** @psalm-suppress UndefinedClass */
        $this->providerRepository = $m2eproHelper->isModuleEnabled()
            ? $objectManager->create(\Ess\M2ePro\Model\AmazonMcf\Amazon\Provider\Account\Repository::class)
            : new Repository\Stub();
    }

    public function isExists(string $merchantId): bool
    {
        return $this->providerRepository->isExists($merchantId);
    }

    public function find(string $merchantId): ?Item
    {
        return $this->providerRepository->find($merchantId);
    }

    /**
     * @return Item[]
     */
    public function getAll(): array
    {
        return $this->providerRepository->getAll();
    }
}
