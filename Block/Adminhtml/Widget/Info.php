<?php

namespace M2E\AmazonMcf\Block\Adminhtml\Widget;

use M2E\AmazonMcf\Helper\Module;
use Magento\Framework\Math\Random;

class Info extends \M2E\AmazonMcf\Block\Adminhtml\AbstractBlock
{
    protected $_template = Module::IDENTIFIER . '::widget/info.phtml';
    private array $info = [];
    private Random $randomMath;

    // ----------------------------------------

    public function __construct(
        Random $random,
        \M2E\AmazonMcf\Block\Adminhtml\Magento\Context\Template $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->randomMath = $random;
    }

    // ----------------------------------------

    public function getInfo()
    {
        return $this->info;
    }

    public function setInfo(array $steps)
    {
        $this->info = $steps;

        return $this;
    }

    // ----------------------------------------

    public function getId()
    {
        if (!$this->hasData('id')) {
            $this->setData('id', 'id-' . $this->randomMath->getRandomString(20));
        }

        return $this->getData('id');
    }

    public function getInfoCount()
    {
        return count($this->getInfo());
    }

    public function getInfoPartWidth($index)
    {
        if (count($this->getInfo()) === 1) {
            return '100%';
        }

        return round(99 / $this->getInfoCount(), 2) . '%';
    }

    public function getInfoPartAlign($index)
    {
        if ($index === 0) {
            return 'left';
        }

        if (($this->getInfoCount() - 1) === $index) {
            return 'right';
        }

        return 'left';
    }

    // ----------------------------------------

    protected function cutLongLines($line)
    {
        if (strlen($line) < 50) {
            return $line;
        }

        return substr($line, 0, 50) . '...';
    }
}
