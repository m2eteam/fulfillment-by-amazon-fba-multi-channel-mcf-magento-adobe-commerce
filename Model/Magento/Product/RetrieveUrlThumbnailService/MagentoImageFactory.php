<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Magento\Product\RetrieveUrlThumbnailService;

class MagentoImageFactory
{
    private \Magento\Framework\ObjectManagerInterface $objectManager;

    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(string $fileName): \Magento\Framework\Image
    {
        return $this->objectManager->create(
            \Magento\Framework\Image::class,
            ['fileName' => $fileName]
        );
    }
}
