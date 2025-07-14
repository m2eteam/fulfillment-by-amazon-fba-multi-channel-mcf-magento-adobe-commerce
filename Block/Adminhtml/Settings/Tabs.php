<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Block\Adminhtml\Settings;

class Tabs extends \M2E\AmazonMcf\Block\Adminhtml\AbstractTabs
{
    public const TAB_ID_CHANNELS = 'channels';

    protected function _construct()
    {
        parent::_construct();

        $this->setId('settings_tabs');
        $this->setDestElementId(
            \M2E\AmazonMcf\Block\Adminhtml\Settings::TABS_CONTAINER_ID
        );
    }

    protected function _prepareLayout()
    {
        $this->css->addFile('settings.css');

        $this->registerTabs();
        $this->setActiveTab($this->getData('active_tab'));

        return parent::_prepareLayout();
    }

    private function registerTabs(): void
    {
        /** @var Tabs\Channels $channelsTab */
        $channelsTab = $this
            ->getLayout()
            ->createBlock(Tabs\Channels::class);

        $this->addTab(self::TAB_ID_CHANNELS, [
            'label' => __('Channels'),
            'title' => __('Channels'),
            'content' => $channelsTab->toHtml(),
        ]);
    }
}
