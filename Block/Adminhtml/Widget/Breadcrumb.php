<?php

namespace M2E\AmazonMcf\Block\Adminhtml\Widget;

class Breadcrumb extends \M2E\AmazonMcf\Block\Adminhtml\AbstractBlock
{
    protected $_template = 'widget/breadcrumb.phtml';

    protected $containerData = [];
    protected $steps = [];
    protected $selectedStep = null;

    public function setContainerData(array $data)
    {
        $this->containerData = $data;

        return $this;
    }

    public function getContainerData($key)
    {
        return isset($this->containerData[$key]) ? $this->containerData[$key] : '';
    }

    public function getSteps()
    {
        return $this->steps;
    }

    public function setSteps(array $steps)
    {
        $this->steps = $steps;

        return $this;
    }

    public function getSelectedStep()
    {
        return $this->selectedStep;
    }

    public function setSelectedStep($stepId)
    {
        $this->selectedStep = $stepId;

        return $this;
    }
}
