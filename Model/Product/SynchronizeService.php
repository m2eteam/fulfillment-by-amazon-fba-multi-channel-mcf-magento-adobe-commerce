<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Product;

class SynchronizeService
{
    private Repository $productRepository;
    private RemoveService $productRemoveService;
    private \M2E\AmazonMcf\Model\ProductFactory $productFactory;
    private \M2E\AmazonMcf\Model\Provider\Amazon\Product\Repository $productProviderRepository;

    public function __construct(
        \M2E\AmazonMcf\Model\Product\Repository $productRepository,
        \M2E\AmazonMcf\Model\Product\RemoveService $productRemoveService,
        \M2E\AmazonMcf\Model\ProductFactory $productFactory,
        \M2E\AmazonMcf\Model\Provider\Amazon\Product\Repository $productProviderRepository
    ) {
        $this->productRepository = $productRepository;
        $this->productRemoveService = $productRemoveService;
        $this->productFactory = $productFactory;
        $this->productProviderRepository = $productProviderRepository;
    }

    public function sync(\M2E\AmazonMcf\Model\Account $account): void
    {
        if (!$account->isEnabled()) {
            return;
        }

        $itemsCollection = $this->productProviderRepository->getItemCollection(
            $account->getMerchantId()
        );
        $products = $this->productRepository->retrieveByAccountId($account->getId());
        if ($itemsCollection->isEmpty()) {
            if (!empty($products)) {
                $this->productRemoveService->removeByAccount($account);
            }

            return;
        }

        $resolved = $this->resolveProducts($products, $itemsCollection);
        $idsOfProductsForRemove = $resolved['idsOfProductsForRemove'];
        $productsBySkuForSave = $resolved['productsBySkuForSave'];

        if (!empty($idsOfProductsForRemove)) {
            $this->productRemoveService->removeProductsByIds($idsOfProductsForRemove);
        }

        $productsForCreateOrSave = $this->retrieveProductsForCreateOrSave(
            $productsBySkuForSave,
            $itemsCollection,
            $account
        );

        if (!empty($productsForCreateOrSave)) {
            $this->productRepository->bulkCreateOrSave($productsForCreateOrSave);
        }
    }

    /**
     * @return array{idsOfProductsForRemove: int[], productsBySkuForSave: list<string, \M2E\AmazonMcf\Model\Product>}
     */
    private function resolveProducts(
        array $products,
        \M2E\AmazonMcf\Model\Provider\Amazon\Product\ItemCollection $itemsCollection
    ): array {
        $idsOfProductsForRemove = [];
        $productsBySkuForSave = [];

        foreach ($products as $product) {
            $item = $itemsCollection->findItemByChannelSku($product->getChannelSku());
            if ($item === null) {
                $idsOfProductsForRemove[] = $product->getId();
                continue;
            }

            $productsBySkuForSave[$product->getChannelSku()] = $product;
        }

        return [
            'idsOfProductsForRemove' => $idsOfProductsForRemove,
            'productsBySkuForSave' => $productsBySkuForSave,
        ];
    }

    /**
     * @param list<string, \M2E\AmazonMcf\Model\Product> $productsBySku
     *
     * @return \M2E\AmazonMcf\Model\Product[]
     */
    private function retrieveProductsForCreateOrSave(
        array $productsBySku,
        \M2E\AmazonMcf\Model\Provider\Amazon\Product\ItemCollection $itemsCollection,
        \M2E\AmazonMcf\Model\Account $account
    ): array {
        $forCreateOrSave = [];
        foreach ($itemsCollection->getItems() as $item) {
            $product = $productsBySku[$item->getChannelSku()] ?? null;
            if ($product !== null) {
                $mappedProduct = $this->getMappedProductWithItemForUpdate($product, $item);
                if ($mappedProduct !== null) {
                    $forCreateOrSave[] = $mappedProduct;
                }
                continue;
            }

            $forCreateOrSave[] = $this->createNewProductByItem($item, $account);
        }

        return $forCreateOrSave;
    }

    private function getMappedProductWithItemForUpdate(
        \M2E\AmazonMcf\Model\Product $product,
        \M2E\AmazonMcf\Model\Provider\Amazon\Product\Item $item
    ): ?\M2E\AmazonMcf\Model\Product {
        $isUpdated = false;

        if ($item->getQty() !== $product->getQty()) {
            $product->setQty($item->getQty());
            $isUpdated = true;
        }

        if ($item->getMagentoProductSku() !== $product->getMagentoProductSku()) {
            $product->setMagentoProductSku($item->getMagentoProductSku());
            $isUpdated = true;
        }

        if (
            $item->isExistsAsin()
            && $item->getAsin() !== $product->findAsin()
        ) {
            $product->setAsin($item->getAsin());
            $isUpdated = true;
        }

        return $isUpdated ? $product : null;
    }

    private function createNewProductByItem(
        \M2E\AmazonMcf\Model\Provider\Amazon\Product\Item $item,
        \M2E\AmazonMcf\Model\Account $account
    ): \M2E\AmazonMcf\Model\Product {
        $product = $this->productFactory
            ->create()
            ->init(
                $account->getId(),
                $item->getChannelSku(),
                $item->getMagentoProductId(),
                $item->getMagentoProductSku(),
                $item->getQty()
            );

        if ($item->isExistsAsin()) {
            $product->setAsin($item->getAsin());
        }

        return $product;
    }
}
