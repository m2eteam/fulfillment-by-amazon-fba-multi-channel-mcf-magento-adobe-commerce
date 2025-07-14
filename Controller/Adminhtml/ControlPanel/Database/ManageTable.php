<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Controller\Adminhtml\ControlPanel\Database;

class ManageTable extends AbstractTable
{
    public function execute()
    {
        $table = $this->getRequest()->getParam('table');

        if ($table === null) {
            return $this->_redirect(
                $this->_backendUrl->getUrl(
                    '*/controlPanel/index',
                    ['tab' => 'database']
                )
            );
        }

        $this->addContent(
            $this->getLayout()->createBlock(
                \M2E\Core\Block\Adminhtml\ControlPanel\Tab\Database\Table::class,
                '',
                ['tableName' => $table],
            ),
        );

        return $this->getResultPage();
    }
}
