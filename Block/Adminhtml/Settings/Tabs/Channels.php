<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Block\Adminhtml\Settings\Tabs;

class Channels extends \M2E\AmazonMcf\Block\Adminhtml\Magento\Form\AbstractForm
{
    public const FIELD_NAME_EBAY = 'ebay';
    public const FIELD_NAME_WALMART = 'walmart';
    public const FIELD_NAME_MAGENTO = 'magento';
    public const FIELD_NAME_TIKTOK_SHOP = 'tts';
    public const FIELD_NAME_KAUFLAND = 'kaufland';
    public const FIELD_NAME_ONBUY = 'onbuy';
    public const FIELD_NAME_OTTO = 'otto';
    public const FIELD_NAME_TEMU = 'temu';

    private const TIKTOK_SHOP_LINK = 'https://commercemarketplace.adobe.com/m2e-tiktok-shop-adobe-commerce.html';
    private const KAUFLAND_LINK = 'https://commercemarketplace.adobe.com/m2e-kaufland-adobe-commerce.html';
    private const ONBUY_LINK = 'https://commercemarketplace.adobe.com/m2e-onbuy-magento-adobe-commerce.html';
    private const OTTO_LINK = 'https://commercemarketplace.adobe.com/m2e-otto-adobe-commerce.html';
    private const TEMU_LINK = 'https://commercemarketplace.adobe.com/m2e-temu-magento-adobe-commerce.html';

    private \M2E\AmazonMcf\Model\ChannelConfig $channelConfig;
    private \M2E\AmazonMcf\Helper\TikTokShop $tikTokShopHelper;
    private \M2E\AmazonMcf\Helper\Kaufland $kauflandHelper;
    private \M2E\AmazonMcf\Helper\Kaufland $onBuyHelper;
    private \M2E\AmazonMcf\Helper\Otto $ottoHelper;
    private \M2E\AmazonMcf\Helper\Temu $temuHelper;

    public function __construct(
        \M2E\AmazonMcf\Model\ChannelConfig $channelConfig,
        \M2E\AmazonMcf\Helper\TikTokShop $tikTokShopHelper,
        \M2E\AmazonMcf\Helper\Kaufland $kauflandHelper,
        \M2E\AmazonMcf\Helper\Kaufland $onBuyHelper,
        \M2E\AmazonMcf\Helper\Otto $ottoHelper,
        \M2E\AmazonMcf\Helper\Temu $temuHelper,
        \M2E\AmazonMcf\Block\Adminhtml\Magento\Context\Template $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        $this->channelConfig = $channelConfig;
        $this->tikTokShopHelper = $tikTokShopHelper;
        $this->kauflandHelper = $kauflandHelper;
        $this->onBuyHelper = $onBuyHelper;
        $this->ottoHelper = $ottoHelper;
        $this->temuHelper = $temuHelper;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset(
            'channels',
            [
                'legend' => __('Enable Amazon Multi-Channel Fulfillment for Channels'),
                'collapsable' => false,
            ]
        );

        $fieldset->addField(
            'magento',
            self::SELECT,
            [
                'name' => self::FIELD_NAME_MAGENTO,
                'label' => __('Magento'),
                'values' => [__('Disabled'), __('Enabled')],
                'value' => $this->channelConfig->isEnabledMagento(),
            ]
        );

        $fieldset->addField(
            'ebay',
            self::SELECT,
            [
                'name' => self::FIELD_NAME_EBAY,
                'label' => __('eBay'),
                'values' => [__('Disabled'), __('Enabled')],
                'value' => $this->channelConfig->isEnabledEbay(),
            ]
        );

        $fieldset->addField(
            'walmart',
            self::SELECT,
            [
                'name' => self::FIELD_NAME_WALMART,
                'label' => __('Walmart'),
                'values' => [__('Disabled'), __('Enabled')],
                'value' => $this->channelConfig->isEnabledWalmart(),
            ]
        );

        if ($this->tikTokShopHelper->isModuleEnabled()) {
            $fieldset->addField(
                'tts',
                self::SELECT,
                [
                    'name' => self::FIELD_NAME_TIKTOK_SHOP,
                    'label' => __('TikTok Shop'),
                    'values' => [__('Disabled'), __('Enabled')],
                    'value' => $this->channelConfig->isEnabledTikTokShop(),
                ]
            );
        } else {
            $fieldset->addField(
                'tts',
                Channels\ChannelLink::class,
                [
                    'label' => __('TikTok Shop'),
                    'value' => __('M2E TikTok Shop Connect'),
                    'postfix' => __('is required'),
                    'href' => self::TIKTOK_SHOP_LINK,
                ]
            );
        }

        if ($this->kauflandHelper->isModuleEnabled()) {
            $fieldset->addField(
                'kaufland',
                self::SELECT,
                [
                    'name' => self::FIELD_NAME_KAUFLAND,
                    'label' => __('Kaufland'),
                    'values' => [__('Disabled'), __('Enabled')],
                    'value' => $this->channelConfig->isEnabledKaufland(),
                ]
            );
        } else {
            $fieldset->addField(
                'kaufland',
                Channels\ChannelLink::class,
                [
                    'label' => __('Kaufland'),
                    'value' => __('M2E Kaufland Connect'),
                    'postfix' => __('is required'),
                    'href' => self::KAUFLAND_LINK,
                ]
            );
        }

        if ($this->ottoHelper->isModuleEnabled()) {
            $fieldset->addField(
                'otto',
                self::SELECT,
                [
                    'name' => self::FIELD_NAME_OTTO,
                    'label' => __('Otto'),
                    'values' => [__('Disabled'), __('Enabled')],
                    'value' => $this->channelConfig->isEnabledOtto(),
                ]
            );
        } else {
            $fieldset->addField(
                'otto',
                Channels\ChannelLink::class,
                [
                    'label' => __('Otto'),
                    'value' => __('M2E Otto Connect'),
                    'postfix' => __('is required'),
                    'href' => self::OTTO_LINK,
                ]
            );
        }

        if ($this->onBuyHelper->isModuleEnabled()) {
            $fieldset->addField(
                'onbuy',
                self::SELECT,
                [
                    'name' => self::FIELD_NAME_ONBUY,
                    'label' => __('OnBuy'),
                    'values' => [__('Disabled'), __('Enabled')],
                    'value' => $this->channelConfig->isEnabledOnBuy(),
                ]
            );
        } else {
            $fieldset->addField(
                'onbuy',
                Channels\ChannelLink::class,
                [
                    'label' => __('OnBuy'),
                    'value' => __('M2E OnBuy Connect'),
                    'postfix' => __('is required'),
                    'href' => self::ONBUY_LINK,
                ]
            );
        }

        if ($this->temuHelper->isModuleEnabled()) {
            $fieldset->addField(
                'temu',
                self::SELECT,
                [
                    'name' => self::FIELD_NAME_TEMU,
                    'label' => __('Temu'),
                    'values' => [__('Disabled'), __('Enabled')],
                    'value' => $this->channelConfig->isEnabledTemu(),
                ]
            );
        } else {
            $fieldset->addField(
                'temu',
                Channels\ChannelLink::class,
                [
                    'label' => __('Temu'),
                    'value' => __('M2E Temu Connect'),
                    'postfix' => __('is required'),
                    'href' => self::TEMU_LINK,
                ]
            );
        }

        $form->setUseContainer(true);

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
