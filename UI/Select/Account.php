<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\UI\Select;

class Account implements \Magento\Framework\Data\OptionSourceInterface
{
    private \M2E\AmazonMcf\Model\Account\Repository $accountRepository;

    public function __construct(\M2E\AmazonMcf\Model\Account\Repository $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    public function toOptionArray()
    {
        $options = [];
        foreach ($this->accountRepository->getAll() as $account) {
            $options[] = [
                'label' => $account->getMerchantId(),
                'value' => $account->getId(),
            ];
        }

        return $options;
    }
}
