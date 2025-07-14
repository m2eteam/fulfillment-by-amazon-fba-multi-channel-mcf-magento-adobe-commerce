<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Product;

class RemoveService
{
    private Repository $productRepository;
    private Log\RemoveService $logRemoveService;
    private Log\Logger $logger;

    public function __construct(
        Repository $productRepository,
        Log\RemoveService $logRemoveService,
        Log\Logger $logger
    ) {
        $this->productRepository = $productRepository;
        $this->logRemoveService = $logRemoveService;
        $this->logger = $logger;
    }

    public function remove(\M2E\AmazonMcf\Model\Product $product): void
    {
        $this->logRemoveService->removeByProduct($product);
        $this->productRepository->delete($product);

        $this->logger->info(
            (string)__(
                'Product with Channel SKU "%channelSku" has been removed.',
                ['channelSku' => $product->getChannelSku()]
            ),
            $product->getId()
        );
    }

    public function removeByAccount(\M2E\AmazonMcf\Model\Account $account): void
    {
        $this->logRemoveService->removeByAccount($account);
        $this->productRepository->deleteByAccountId($account->getId());
    }

    /**
     * @param int[] $productsIds
     */
    public function removeProductsByIds(array $productsIds): void
    {
        $this->logRemoveService->removeByProductsIds($productsIds);
        $this->productRepository->deleteByIds($productsIds);
    }
}
