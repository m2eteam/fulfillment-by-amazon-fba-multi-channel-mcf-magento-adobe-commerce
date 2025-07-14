<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Controller\Adminhtml\ControlPanel\Database;

class ShowOperationHistoryExecutionTreeUp extends AbstractTable
{
    private \M2E\AmazonMcf\Model\OperationHistory\Repository $repository;

    public function __construct(
        \M2E\AmazonMcf\Model\OperationHistory\Repository $repository,
        \M2E\Core\Model\ControlPanel\Database\TableModelFactory $databaseTableFactory,
        \M2E\AmazonMcf\Helper\Module $moduleHelper,
        \M2E\AmazonMcf\Helper\Data\Cache\Permanent $permanentCache,
        \M2E\AmazonMcf\Controller\Adminhtml\Context $context
    ) {
        parent::__construct($databaseTableFactory, $moduleHelper, $permanentCache, $context);

        $this->repository = $repository;
    }

    public function execute()
    {
        $operationHistoryId = $this->getRequest()->getParam('operation_history_id');
        if (empty($operationHistoryId)) {
            $this->getMessageManager()->addErrorMessage('Operation history ID is not presented.');

            $this->redirectToTablePage(
                \M2E\AmazonMcf\Helper\Module\Database\Tables::TABLE_NAME_OPERATION_HISTORY,
            );
        }

        $operationHistory = $this->repository->get((int)$operationHistoryId);
        $operationHistory->setObject($operationHistoryId);

        $this->getResponse()->setBody(
            '<pre>' . $operationHistory->getExecutionTreeUpInfo() . '</pre>',
        );
    }
}
