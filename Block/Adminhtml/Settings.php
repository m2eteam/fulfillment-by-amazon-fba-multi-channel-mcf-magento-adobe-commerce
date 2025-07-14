<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Block\Adminhtml;

class Settings extends AbstractContainer
{
    public const TABS_CONTAINER_ID = 'tabs_container';

    protected function _construct()
    {
        parent::_construct();

        $this->removeButton('back');
        $this->removeButton('reset');
        $this->removeButton('delete');
        $this->removeButton('add');
        $this->removeButton('save');
        $this->removeButton('edit');

        $this->addButton('save', [
            'label' => __('Save'),
            'onclick' => 'SettingsObj.saveSettings()',
            'class' => 'primary',
        ]);
    }

    protected function _toHtml()
    {
        return parent::_toHtml() . sprintf('<div id="%s"></div>', self::TABS_CONTAINER_ID);
    }

    protected function _beforeToHtml()
    {
        $this->jsUrl->add(
            $this->getUrl('*/settings/save'),
            \M2E\AmazonMcf\Block\Adminhtml\Settings\Tabs::TAB_ID_CHANNELS
        );

        $this->jsTranslator->addTranslations([
            'Settings saved' => __('Settings saved'),
            'Error' => __('Error'),
        ]);
        $this->js->addRequireJs(
            ['settings' => 'AmazonMcf/Settings'],
            'window.SettingsObj = new Settings();'
        );

        return parent::_beforeToHtml();
    }
}
