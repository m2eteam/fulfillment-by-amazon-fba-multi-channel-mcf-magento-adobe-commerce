<?php

namespace M2E\AmazonMcf\Block\Adminhtml\Magento\Renderer;

class CssRenderer
{
    protected array $css = [];
    protected array $cssFiles = [];

    public function add($css)
    {
        $this->css[] = $css;

        return $this;
    }

    public function addFile($file)
    {
        $this->cssFiles[] = $file;

        return $this;
    }

    public function getFiles()
    {
        return $this->cssFiles;
    }

    public function render()
    {
        return implode($this->css);
    }
}
