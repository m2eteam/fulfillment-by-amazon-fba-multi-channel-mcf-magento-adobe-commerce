<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\OperationHistory;

class Repository
{
    private \M2E\AmazonMcf\Model\ResourceModel\OperationHistory $resourceModel;
    private \M2E\AmazonMcf\Model\OperationHistoryFactory $operationHistoryFactory;

    public function __construct(
        \M2E\AmazonMcf\Model\ResourceModel\OperationHistory $resourceModel,
        \M2E\AmazonMcf\Model\OperationHistoryFactory $operationHistoryFactory
    ) {
        $this->resourceModel = $resourceModel;
        $this->operationHistoryFactory = $operationHistoryFactory;
    }

    public function find(int $id): ?\M2E\AmazonMcf\Model\OperationHistory
    {
        $model = $this->operationHistoryFactory->create();
        $this->resourceModel->load($model, $id);
        if ($model->isObjectNew()) {
            return null;
        }

        return $model;
    }

    public function get(int $id): \M2E\AmazonMcf\Model\OperationHistory
    {
        $model = $this->find($id);
        if ($model === null) {
            throw new \M2E\AmazonMcf\Model\Exception\Logic('Entity not found by id ' . $id);
        }

        return $model;
    }

    public function clear(\DateTime $borderDate): void
    {
        $minDate = $borderDate->format('Y-m-d H:i:s');

        $this->resourceModel->getConnection()->delete(
            $this->resourceModel->getMainTable(),
            "start_date <= '$minDate'"
        );
    }
}
