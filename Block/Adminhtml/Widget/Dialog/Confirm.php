<?php

namespace M2E\AmazonMcf\Block\Adminhtml\Widget\Dialog;

class Confirm extends \M2E\AmazonMcf\Block\Adminhtml\AbstractBlock
{
    public function _construct()
    {
        parent::_construct();

        // Initialization block
        // ---------------------------------------
        $this->setId('widgetConfirm');
        // ---------------------------------------

        $this->setTemplate('widget/dialog/confirm.phtml');
    }

    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();

        $data = [
            'class' => 'ok_button',
            'label' => __('Confirm'),
            'onclick' => 'Dialog.okCallback();',
        ];
        $buttonBlock = $this->getLayout()
                            ->createBlock(
                                \M2E\AmazonMcf\Block\Adminhtml\Magento\Button::class
                            )
                            ->setData($data);

        $this->setChild('ok_button', $buttonBlock);
    }
}
