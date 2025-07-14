<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Block\Adminhtml\System\Config;

use Magento\Framework\Data\Form\Element\AbstractElement;

class EnabledModule extends \Magento\Config\Block\System\Config\Form\Field
{
    private \M2E\AmazonMcf\Model\Module $module;

    public function __construct(
        \M2E\AmazonMcf\Model\Module $module,
        \Magento\Backend\Block\Template\Context $context,
        array $data = [],
        ?\Magento\Framework\View\Helper\SecureHtmlRenderer $secureRenderer = null
    ) {
        parent::__construct($context, $data, $secureRenderer);

        $this->module = $module;
    }

    protected function _getElementHtml(AbstractElement $element): string
    {
        $isModuleEnabled = !$this->module->isDisabled();
        $options = [
            ['value' => 1, 'label' => __('Yes'), 'selected' => $isModuleEnabled],
            ['value' => 0, 'label' => __('No'), 'selected' => !$isModuleEnabled],
        ];

        $html = sprintf(
            '<select id="%s" name="%s" class="select admin__control-select">',
            $element->getHtmlId(),
            $element->getName()
        );

        foreach ($options as $option) {
            $selected = $option['selected'] ?  'selected="selected"' : '';
            $html .= sprintf('<option value="%s" %s>%s</option>', $option['value'], $selected, $option['label']);
        }
        $html .= '</select>';

        return $html;
    }
}
