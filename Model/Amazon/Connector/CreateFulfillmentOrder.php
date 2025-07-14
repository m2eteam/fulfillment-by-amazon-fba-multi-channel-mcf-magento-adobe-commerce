<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Amazon\Connector;

use Ess\M2ePro\Model\AmazonMcf\Amazon\Connector\CreateFulfillmentOrder\Processor as M2EProCreateFulfillmentOrder;

class CreateFulfillmentOrder
{
    /** @var M2EProCreateFulfillmentOrder|CreateFulfillmentOrder\BaseInterface */
    private $connectProcessor;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \M2E\AmazonMcf\Helper\M2EPro $m2eproHelper
    ) {
        /** @psalm-suppress UndefinedClass */
        $this->connectProcessor = $m2eproHelper->isModuleEnabled()
            ? $objectManager->create(M2EProCreateFulfillmentOrder::class)
            : new CreateFulfillmentOrder\Stub();
    }

    /**
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\AuthorizationException
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\SystemUnavailableException
     * @throws \M2E\AmazonMcf\Model\Amazon\Connector\Exception\ThrottlingException
     */
    public function process(
        string $merchantId,
        CreateFulfillmentOrder\Request $request
    ): CreateFulfillmentOrder\Response {
        return $this->connectProcessor->process($merchantId, $request);
    }
}
