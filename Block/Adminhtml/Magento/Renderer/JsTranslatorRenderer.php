<?php

namespace M2E\AmazonMcf\Block\Adminhtml\Magento\Renderer;

class JsTranslatorRenderer
{
    protected $jsTranslations = [];

    public function add($alias, $translation)
    {
        $this->jsTranslations[$alias] = $translation;

        return $this;
    }

    public function addTranslations(array $translations)
    {
        $this->jsTranslations = array_merge($this->jsTranslations, $translations);

        return $this;
    }

    public function render()
    {
        if (empty($this->jsTranslations)) {
            return '';
        }

        $translations = \M2E\Core\Helper\Json::encode($this->jsTranslations);

        return "AmazonMcf.translator.add({$translations});";
    }
}
