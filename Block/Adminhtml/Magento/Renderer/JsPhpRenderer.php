<?php

namespace M2E\AmazonMcf\Block\Adminhtml\Magento\Renderer;

class JsPhpRenderer
{
    protected $jsPhp = [];

    public function addConstants(array $constants)
    {
        $this->jsPhp = array_merge($this->jsPhp, $constants);

        return $this;
    }

    public function render()
    {
        if (empty($this->jsPhp)) {
            return '';
        }

        $constants = \M2E\Core\Helper\Json::encode($this->jsPhp);

        return "AmazonMcf.php.add({$constants});";
    }
}
