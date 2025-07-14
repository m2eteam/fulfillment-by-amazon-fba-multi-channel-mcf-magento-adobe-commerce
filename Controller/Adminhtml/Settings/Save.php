<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Controller\Adminhtml\Settings;

class Save extends \M2E\AmazonMcf\Controller\Adminhtml\AbstractBase
{
    private \M2E\AmazonMcf\Model\ChannelConfig $channelConfig;
    private \M2E\AmazonMcf\Helper\TikTokShop $tikTokShopHelper;
    private \M2E\AmazonMcf\Helper\Kaufland $kauflandHelper;
    private \M2E\AmazonMcf\Helper\OnBuy $onBuyHelper;
    private \M2E\AmazonMcf\Helper\Otto $ottoHelper;
    private \M2E\AmazonMcf\Helper\Temu $temuHelper;

    public function __construct(
        \M2E\AmazonMcf\Model\ChannelConfig $channelConfig,
        \M2E\AmazonMcf\Helper\TikTokShop $tikTokShopHelper,
        \M2E\AmazonMcf\Helper\Kaufland $kauflandHelper,
        \M2E\AmazonMcf\Helper\OnBuy $onBuyHelper,
        \M2E\AmazonMcf\Helper\Otto $ottoHelper,
        \M2E\AmazonMcf\Helper\Temu $temuHelper,
        \M2E\AmazonMcf\Controller\Adminhtml\Context $context
    ) {
        parent::__construct($context);

        $this->channelConfig = $channelConfig;
        $this->tikTokShopHelper = $tikTokShopHelper;
        $this->kauflandHelper = $kauflandHelper;
        $this->onBuyHelper = $onBuyHelper;
        $this->ottoHelper = $ottoHelper;
        $this->temuHelper = $temuHelper;
    }

    public function execute()
    {
        $isSuccess = true;

        try {
            $this->saveSettings(
                $this->getRequest()->getPostValue()
            );
        } catch (\Throwable $e) {
            $isSuccess = false;
        }

        $this->setAjaxContent(
            json_encode(['success' => $isSuccess])
        );

        return $this->getResult();
    }

    private function saveSettings(array $requestParams): void
    {
        $tab = $requestParams['tab'];

        if ($tab === \M2E\AmazonMcf\Block\Adminhtml\Settings\Tabs::TAB_ID_CHANNELS) {
            $this->saveChannels($requestParams);
        }
    }

    private function saveChannels(array $requestParams): void
    {
        $requestParams[\M2E\AmazonMcf\Block\Adminhtml\Settings\Tabs\Channels::FIELD_NAME_EBAY]
            ? $this->channelConfig->enableEbay()
            : $this->channelConfig->disableEbay();

        $requestParams[\M2E\AmazonMcf\Block\Adminhtml\Settings\Tabs\Channels::FIELD_NAME_WALMART]
            ? $this->channelConfig->enableWalmart()
            : $this->channelConfig->disableWalmart();

        $requestParams[\M2E\AmazonMcf\Block\Adminhtml\Settings\Tabs\Channels::FIELD_NAME_MAGENTO]
            ? $this->channelConfig->enableMagento()
            : $this->channelConfig->disableMagento();

        if ($this->tikTokShopHelper->isModuleEnabled()) {
            $requestParams[\M2E\AmazonMcf\Block\Adminhtml\Settings\Tabs\Channels::FIELD_NAME_TIKTOK_SHOP]
                ? $this->channelConfig->enableTikTokShop()
                : $this->channelConfig->disableTikTokShop();
        }

        if ($this->kauflandHelper->isModuleEnabled()) {
            $requestParams[\M2E\AmazonMcf\Block\Adminhtml\Settings\Tabs\Channels::FIELD_NAME_KAUFLAND]
                ? $this->channelConfig->enableKaufland()
                : $this->channelConfig->disableKaufland();
        }

        if ($this->onBuyHelper->isModuleEnabled()) {
            $requestParams[\M2E\AmazonMcf\Block\Adminhtml\Settings\Tabs\Channels::FIELD_NAME_ONBUY]
                ? $this->channelConfig->enableOnbuy()
                : $this->channelConfig->disableOnbuy();
        }

        if ($this->ottoHelper->isModuleEnabled()) {
            $requestParams[\M2E\AmazonMcf\Block\Adminhtml\Settings\Tabs\Channels::FIELD_NAME_OTTO]
                ? $this->channelConfig->enableOtto()
                : $this->channelConfig->disableOtto();
        }

        if ($this->temuHelper->isModuleEnabled()) {
            $requestParams[\M2E\AmazonMcf\Block\Adminhtml\Settings\Tabs\Channels::FIELD_NAME_TEMU]
                ? $this->channelConfig->enableTemu()
                : $this->channelConfig->disableTemu();
        }
    }
}
