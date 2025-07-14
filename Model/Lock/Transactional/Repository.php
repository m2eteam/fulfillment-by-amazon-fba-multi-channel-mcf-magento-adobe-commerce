<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Lock\Transactional;

use M2E\AmazonMcf\Model\ResourceModel\Lock\Transactional as ResourceLockTransactional;

class Repository
{
    private \M2E\AmazonMcf\Model\Lock\TransactionalFactory $transactionalFactory;
    private \M2E\AmazonMcf\Model\ResourceModel\Lock\Transactional $transactionalResource;

    public function __construct(
        \M2E\AmazonMcf\Model\Lock\TransactionalFactory $transactionalFactory,
        \M2E\AmazonMcf\Model\ResourceModel\Lock\Transactional $transactionalResource
    ) {
        $this->transactionalFactory = $transactionalFactory;
        $this->transactionalResource = $transactionalResource;
    }

    public function save(\M2E\AmazonMcf\Model\Lock\Transactional $transactional): void
    {
        $this->transactionalResource->save($transactional);
    }

    public function findByNick(string $nick): ?\M2E\AmazonMcf\Model\Lock\Transactional
    {
        $model = $this->transactionalFactory->create();
        $this->transactionalResource->load(
            $model,
            $nick,
            ResourceLockTransactional::COLUMN_NICK
        );

        if ($model->isObjectNew()) {
            return null;
        }

        return $model;
    }
}
