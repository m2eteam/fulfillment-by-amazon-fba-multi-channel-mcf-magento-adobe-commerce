<?php

namespace M2E\AmazonMcf\Controller\Adminhtml\ControlPanel\Database;

class DeleteTableRows extends AbstractTable
{
    public function execute()
    {
        $ids = $this->prepareIds();
        $modelInstance = $this->getTableModel();

        if (empty($ids)) {
            $this->getMessageManager()->addErrorMessage("Failed to get model or any of Table Rows are not selected.");
            $this->redirectToTablePage($modelInstance->getTableName());
        }

        $modelInstance->deleteEntries($ids);
        $this->afterTableAction($modelInstance->getTableName());
    }
}
