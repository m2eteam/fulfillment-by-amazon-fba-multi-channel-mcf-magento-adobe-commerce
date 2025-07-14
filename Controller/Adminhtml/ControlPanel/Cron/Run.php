<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Controller\Adminhtml\ControlPanel\Cron;

class Run extends \M2E\AmazonMcf\Controller\Adminhtml\AbstractBase
{
    private \M2E\AmazonMcf\Model\Cron\Runner\Developer $cronRunner;

    public function __construct(
        \M2E\AmazonMcf\Model\Cron\Runner\Developer $cronRunner,
        \M2E\AmazonMcf\Controller\Adminhtml\Context $context
    ) {
        parent::__construct($context);

        $this->cronRunner = $cronRunner;
    }

    public function execute()
    {
        $taskCode = $this->getRequest()->getParam('task_code');

        if (!empty($taskCode)) {
            $this->cronRunner->setAllowedTasks([$taskCode]);
        }

        $this->cronRunner->process();
        $dataInfo = $this->cronRunner
            ->getOperationHistory()
            ->getFullDataInfo();

        $this->getResponse()->setBody('<pre>' . $dataInfo . '</pre>');
    }
}
