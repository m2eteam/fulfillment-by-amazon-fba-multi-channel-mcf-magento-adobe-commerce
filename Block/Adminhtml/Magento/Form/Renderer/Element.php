<?php

namespace M2E\AmazonMcf\Block\Adminhtml\Magento\Form\Renderer;

use Magento\Backend\Block\Widget\Form\Renderer\Fieldset\Element as MagentoElement;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Element extends MagentoElement
{
    protected function getTooltipHtml($content)
    {
        return <<<HTML
<div class="amazonmcf-field-tooltip admin__field-tooltip">
    <a class="admin__field-tooltip-action" href="javascript://"></a>
    <div class="admin__field-tooltip-content">
        {$content}
    </div>
</div>
HTML;
    }

    public function render(AbstractElement $element)
    {
        $isRequired = $element->getData('required');

        if ($isRequired === true) {
            $element->removeClass('required-entry');
            $element->removeClass('_required');
            $element->setClass('amazonmcf-required-when-visible ' . $element->getClass());
        }

        $tooltip = $element->getData('tooltip');

        if ($tooltip === null) {
            $element->addClass('amazonmcf-field-without-tooltip');

            return parent::render($element);
        }

        $element->setAfterElementHtml(
            $element->getAfterElementHtml() . $this->getTooltipHtml($tooltip)
        );

        $element->addClass('amazonmcf-field-with-tooltip');

        return parent::render($element);
    }
}
