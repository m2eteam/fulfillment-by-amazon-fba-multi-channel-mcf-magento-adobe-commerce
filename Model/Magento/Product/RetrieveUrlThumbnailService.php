<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Magento\Product;

class RetrieveUrlThumbnailService
{
    private const RESIZED_THUMBNAIL_CACHE_TIME = 604800;
    private const RESIZE_IMAGE_WIDTH = 100;
    private const RESIZE_IMAGE_HEIGHT = 100;

    private RetrieveUrlThumbnailService\Repository $repository;
    private RetrieveUrlThumbnailService\MagentoImageFactory $magentoImageFactory;
    private \Magento\Framework\Filesystem $filesystem;
    private \Magento\Store\Model\StoreManagerInterface $storeManager;
    private \M2E\AmazonMcf\Helper\Data\Cache\Permanent $cache;
    private \Magento\Framework\Filesystem\DriverInterface $fsDriver;

    public function __construct(
        RetrieveUrlThumbnailService\Repository $repository,
        RetrieveUrlThumbnailService\MagentoImageFactory $magentoImageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem $filesystem,
        \M2E\AmazonMcf\Helper\Data\Cache\Permanent $cache,
        \Magento\Framework\Filesystem\DriverPool $driverPool
    ) {
        $this->repository = $repository;
        $this->magentoImageFactory = $magentoImageFactory;
        $this->filesystem = $filesystem;
        $this->storeManager = $storeManager;
        $this->cache = $cache;
        $this->fsDriver = $driverPool->getDriver(\Magento\Framework\Filesystem\DriverPool::FILE);
    }

    public function process(int $magentoProductId): ?string
    {
        $storeId = \Magento\Store\Model\Store::DEFAULT_STORE_ID;

        $attributeId = $this->getAttributeId();
        $productThumbnailPath = $this->repository->findProductThumbnailPath(
            $magentoProductId,
            $attributeId,
            $storeId
        );
        if ($productThumbnailPath === null) {
            return null;
        }

        $thumbnailPath = $this->buildThumbnailPath($productThumbnailPath);
        if (!$this->fsDriver->isExists($thumbnailPath)) {
            return null;
        }

        $resizedThumbnailPath = $this->buildResizedThumbnailPath($thumbnailPath);
        if ($this->fsDriver->isFile($resizedThumbnailPath)) {
            if ($this->isResizingCacheFresh($resizedThumbnailPath)) {
                return $this->getUrlByPath($storeId, $resizedThumbnailPath);
            }

            $this->fsDriver->deleteFile($resizedThumbnailPath);
        }

        $this->saveResizedThumbnail($thumbnailPath, $resizedThumbnailPath);
        if (!$this->fsDriver->isFile($resizedThumbnailPath)) {
            return null;
        }

        return $this->getUrlByPath($storeId, $resizedThumbnailPath);
    }

    private function getAttributeId(): int
    {
        $attributeId = $this->cache->getValue(__METHOD__);
        if ($attributeId === null) {
            $attributeId = $this->repository->getProductThumbnailAttributeId();
            $this->cache->setValue(__METHOD__, $attributeId, 60 * 60);
        }

        return $attributeId;
    }

    private function buildThumbnailPath(string $productThumbnailPath): string
    {
        $absolutePath = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)
                                         ->getAbsolutePath();

        return $absolutePath . 'catalog/product/' . ltrim($productThumbnailPath, '/');
    }

    private function buildResizedThumbnailPath(string $path): string
    {
        $prefixResizedImage = sprintf(
            'resized-%dpx-%dpx-',
            self::RESIZE_IMAGE_WIDTH,
            self::RESIZE_IMAGE_HEIGHT
        );

        return $this->fsDriver->getParentDirectory($path)
            . DIRECTORY_SEPARATOR
            . $prefixResizedImage
            . basename($path);
    }

    private function isResizingCacheFresh(string $resizedThumbnailPath): bool
    {
        $currentTime = \M2E\Core\Helper\Date::createCurrentGmt()->getTimestamp();

        return filemtime($resizedThumbnailPath) + self::RESIZED_THUMBNAIL_CACHE_TIME >= $currentTime;
    }

    private function saveResizedThumbnail(string $thumbnailPath, string $resizedThumbnailPath): void
    {
        try {
            $magentoImage = $this->magentoImageFactory->create($thumbnailPath);
            $magentoImage->constrainOnly(true);
            $magentoImage->keepAspectRatio(true);
            $magentoImage->keepFrame(false);
            $magentoImage->resize(self::RESIZE_IMAGE_WIDTH, self::RESIZE_IMAGE_HEIGHT);
            $magentoImage->save($resizedThumbnailPath);
        } catch (\Throwable $exception) {
        }
    }

    private function getUrlByPath(int $storeId, string $path): string
    {
        $baseMediaUrl = $this->storeManager
            ->getStore($storeId)
            ->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            );
        $baseMediaPath = $this->filesystem
            ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)
            ->getAbsolutePath();

        $imageLink = str_replace($baseMediaPath, $baseMediaUrl, $path);
        $imageLink = str_replace(DIRECTORY_SEPARATOR, '/', $imageLink);

        return str_replace(' ', '%20', $imageLink);
    }
}
