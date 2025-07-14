<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Controller\Adminhtml;

class AclAuthorization
{
    private \Magento\Framework\AuthorizationInterface $authorization;

    public function __construct(\Magento\Framework\AuthorizationInterface $authorization)
    {
        $this->authorization = $authorization;
    }

    public function isAllowedGeneral(): bool
    {
        return $this->authorization->isAllowed('M2E_AmazonMcf::amazonmcf_module_general');
    }
}
