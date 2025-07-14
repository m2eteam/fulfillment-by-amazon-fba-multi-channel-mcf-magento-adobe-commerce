<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Controller\Adminhtml\Product\Action;

class Enable extends \M2E\AmazonMcf\Controller\Adminhtml\AbstractBase
{
    use ActionTrait;

    private \M2E\AmazonMcf\Model\Product\Repository $productRepository;
    private \M2E\AmazonMcf\Model\ResourceModel\Product\Grid\MassActionFilter $massActionFilter;

    public function __construct(
        \M2E\AmazonMcf\Model\Product\Repository $productRepository,
        \M2E\AmazonMcf\Model\ResourceModel\Product\Grid\MassActionFilter $massActionFilter,
        \M2E\AmazonMcf\Controller\Adminhtml\Context $context
    ) {
        parent::__construct($context);

        $this->productRepository = $productRepository;
        $this->massActionFilter = $massActionFilter;
    }

    public function execute()
    {
        $this->productRepository->enableByMassActionFilter($this->massActionFilter);

        $this->getMessageManager()->addSuccessMessage(
            __('MCF synchronization has been enabled for the selected Products')
        );

        return $this->redirectToGrid();
    }
}
