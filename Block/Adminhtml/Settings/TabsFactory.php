<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Block\Adminhtml\Settings;

class TabsFactory
{
    public function createWithActiveTabChannels(\Magento\Framework\View\LayoutInterface $layout): Tabs
    {
        return $this->create(Tabs::TAB_ID_CHANNELS, $layout);
    }

    private function create(string $activeTab, \Magento\Framework\View\LayoutInterface $layout): Tabs
    {
        /** @var Tabs */
        return $layout->createBlock(
            Tabs::class,
            '',
            [
                'data' => [
                    'active_tab' => $activeTab,
                ],
            ]
        );
    }
}
