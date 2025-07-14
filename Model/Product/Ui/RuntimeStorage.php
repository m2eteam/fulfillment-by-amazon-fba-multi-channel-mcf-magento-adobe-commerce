<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Product\Ui;

class RuntimeStorage
{
    private \M2E\AmazonMcf\Model\Product\Repository $repository;
    /** @var \M2E\AmazonMcf\Model\Product[] */
    private array $products;

    public function __construct(\M2E\AmazonMcf\Model\Product\Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param int[] $ids
     *
     * @return void
     */
    public function loadByIds(array $ids): void
    {
        $products = [];
        foreach ($this->repository->retrieveByIds($ids) as $product) {
            $products[$product->getId()] = $product;
        }

        $this->products = $products;
    }

    public function findProduct(int $id): ?\M2E\AmazonMcf\Model\Product
    {
        if (!isset($this->products)) {
            return null;
        }

        return $this->products[$id] ?? null;
    }

    /**
     * @return \M2E\AmazonMcf\Model\Product[]
     */
    public function getAll(): array
    {
        if (!isset($this->products)) {
            throw new \LogicException('Products was not initialized.');
        }

        return $this->products;
    }
}
