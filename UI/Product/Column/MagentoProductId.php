<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\UI\Product\Column;

class MagentoProductId extends \Magento\Ui\Component\Listing\Columns\Column
{
    private \M2E\AmazonMcf\Helper\Magento\Url $magentoUrlHelper;
    private \M2E\AmazonMcf\Model\Magento\Product\RetrieveUrlThumbnailService $retrieveUrlThumbnailService;

    public function __construct(
        \M2E\AmazonMcf\Helper\Magento\Url $magentoUrlHelper,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \M2E\AmazonMcf\Model\Magento\Product\RetrieveUrlThumbnailService $retrieveUrlThumbnailService,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->magentoUrlHelper = $magentoUrlHelper;
        $this->retrieveUrlThumbnailService = $retrieveUrlThumbnailService;
    }

    public function prepareDataSource(array $dataSource): array
    {
        if (empty($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach ($dataSource['data']['items'] as &$item) {
            if (empty($item['entity_id'])) {
                $item['entity_id'] = (string)__('N/A');
                continue;
            }

            $item['entity_id'] = $this->getHtml((int)$item['entity_id']);
        }

        return $dataSource;
    }

    private function getHtml(int $entityId): string
    {
        $url = $this->magentoUrlHelper->getUrlCatalogProductEdit($entityId);
        $urlThumbnail = $this->retrieveUrlThumbnail($entityId);
        if ($urlThumbnail === null) {
            return sprintf('<a href="%s" target="_blank">%d</a>', $url, $entityId);
        }

        return sprintf(
            '<a href="%s" target="_blank">%d <div style="margin-top: 5px">'
            . '<img style="max-width: 100px; max-height: 100px;" src="%s"/>'
            . '</div></a>',
            $url,
            $entityId,
            $urlThumbnail
        );
    }

    private function retrieveUrlThumbnail(int $entityId): ?string
    {
        try {
            return $this->retrieveUrlThumbnailService->process($entityId);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
