<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Plugin\Block\Tracking\PopupPlugin;

class DecodedHashDTO
{
    public string $entityType;
    public int $entityId;

    public function __construct(string $entityType, int $entityId)
    {
        $this->entityType = $entityType;
        $this->entityId = $entityId;
    }
}
