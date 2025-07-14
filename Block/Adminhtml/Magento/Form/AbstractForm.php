<?php

namespace M2E\AmazonMcf\Block\Adminhtml\Magento\Form;

use M2E\AmazonMcf\Block\Adminhtml\Traits;

abstract class AbstractForm extends \Magento\Backend\Block\Widget\Form\Generic
{
    use Traits\BlockTrait;
    use Traits\RendererTrait;

    public const SELECT = Element\Select::class;
    public const HELP_BLOCK = Element\HelpBlock::class;

    public function __construct(
        \M2E\AmazonMcf\Block\Adminhtml\Magento\Context\Template $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        $this->css = $context->getCss();
        $this->jsPhp = $context->getJsPhp();
        $this->js = $context->getJs();
        $this->jsTranslator = $context->getJsTranslator();
        $this->jsUrl = $context->getJsUrl();

        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        \Magento\Framework\Data\Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock(\M2E\AmazonMcf\Block\Adminhtml\Magento\Form\Renderer\Element::class)
        );

        \Magento\Framework\Data\Form::setFieldsetRenderer(
            $this->getLayout()->createBlock(\M2E\AmazonMcf\Block\Adminhtml\Magento\Form\Renderer\Fieldset::class)
        );

        return $this;
    }
}
