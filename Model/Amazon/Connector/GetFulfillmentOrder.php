<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Amazon\Connector;

use Ess\M2ePro\Model\AmazonMcf\Amazon\Connector\GetFulfillmentOrder\Processor as M2EProGetFulfillmentOrder;

class GetFulfillmentOrder
{
    /** @var M2EProGetFulfillmentOrder|GetFulfillmentOrder\BaseInterface */
    private $connectProcessor;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \M2E\AmazonMcf\Helper\M2EPro $m2eproHelper
    ) {
        /** @psalm-suppress UndefinedClass */
        $this->connectProcessor = $m2eproHelper->isModuleEnabled()
            ? $objectManager->create(M2EProGetFulfillmentOrder::class)
            : new GetFulfillmentOrder\Stub();
    }

    /**
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\AuthorizationException
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\SystemUnavailableException
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\ThrottlingException
     */
    public function process(string $merchantId, string $sellerFulfillmentId): GetFulfillmentOrder\Response
    {
        return $this->connectProcessor->process($merchantId, $sellerFulfillmentId);
    }
}
