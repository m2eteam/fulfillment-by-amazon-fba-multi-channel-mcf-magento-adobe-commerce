<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\ControlPanel;

use M2E\Core\Model\ControlPanel\Tab;

class TabProvider implements \M2E\Core\Model\ControlPanel\Tab\ProviderInterface
{
    /** @var Tab[] */
    private array $tabs;

    public function getExtensionModuleName(): string
    {
        return \M2E\AmazonMcf\Model\ControlPanel\Extension::NAME;
    }

    public function getTabs(): array
    {
        if (isset($this->tabs)) {
            return $this->tabs;
        }

        return $this->tabs = [
            new Tab(
                \M2E\Core\Block\Adminhtml\ControlPanel\Tab\Overview::class,
                'm2e_amazonmcf/controlPanel/index',
            ),
            new Tab(
                \M2E\Core\Block\Adminhtml\ControlPanel\Tab\Database::class,
                'm2e_amazonmcf/controlPanel/databaseTab',
                [],
                true
            ),
            new Tab(
                \M2E\Core\Block\Adminhtml\ControlPanel\Tab\Cron::class,
                'm2e_amazonmcf/controlPanel/index'
            ),
        ];
    }
}
