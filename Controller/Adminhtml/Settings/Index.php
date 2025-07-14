<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Controller\Adminhtml\Settings;

class Index extends \M2E\AmazonMcf\Controller\Adminhtml\AbstractBase
{
    private \M2E\AmazonMcf\Block\Adminhtml\Settings\TabsFactory $tabsFactory;
    private \M2E\AmazonMcf\Controller\Adminhtml\AclAuthorization $aclAuthorization;

    public function __construct(
        \M2E\AmazonMcf\Controller\Adminhtml\Context $context,
        \M2E\AmazonMcf\Block\Adminhtml\Settings\TabsFactory $tabsFactory,
        \M2E\AmazonMcf\Controller\Adminhtml\AclAuthorization $aclAuthorization
    ) {
        parent::__construct($context);

        $this->aclAuthorization = $aclAuthorization;
        $this->tabsFactory = $tabsFactory;
    }

    public function execute()
    {
        $tabsBlock = $this->tabsFactory->createWithActiveTabChannels(
            $this->getLayout()
        );
        $this->addLeft($tabsBlock);

        $this->addContent(
            $this->getLayout()
                 ->createBlock(\M2E\AmazonMcf\Block\Adminhtml\Settings::class)
        );

        $this->getResult()
             ->getConfig()
             ->getTitle()
             ->prepend(__('Settings'));

        return $this->getResult();
    }

    protected function _isAllowed(): bool
    {
        return $this->aclAuthorization->isAllowedGeneral();
    }

    protected function getLayoutType()
    {
        return self::LAYOUT_TWO_COLUMNS;
    }
}
