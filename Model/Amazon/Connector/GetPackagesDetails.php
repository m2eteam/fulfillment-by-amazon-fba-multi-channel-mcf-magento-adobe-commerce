<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Amazon\Connector;

class GetPackagesDetails
{
    /** @var \Ess\M2ePro\Model\AmazonMcf\Amazon\Connector\GetPackageDetails\Processor|GetPackageDetails\BaseInterface */
    private $connectProcessor;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \M2E\AmazonMcf\Helper\M2EPro $m2eproHelper
    ) {
        /** @psalm-suppress UndefinedClass */
        $this->connectProcessor = $m2eproHelper->isModuleEnabled()
            ? $objectManager->create(\Ess\M2ePro\Model\AmazonMcf\Amazon\Connector\GetPackageDetails\Processor::class)
            : new GetPackageDetails\Stub();
    }

    /**
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\AuthorizationException
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\SystemUnavailableException
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\ThrottlingException
     */
    public function process(string $merchantId, int $packageNumber): GetPackageDetails\Response
    {
        return $this->connectProcessor->process($merchantId, $packageNumber);
    }
}
