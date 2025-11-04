<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Plugin\Block\Tracking\PopupPlugin;

class TrackDataDTO extends \Magento\Shipping\Model\Tracking\Result\AbstractResult
{
    private string $carrierTitle;
    private string $tracking;
    private string $url;

    public function __construct(
        string $carrierTitle,
        string $tracking,
        string $url,
        array $data = []
    ) {
        parent::__construct($data);
        $this->carrierTitle = $carrierTitle;
        $this->tracking = $tracking;
        $this->url = $url;
    }

    public function getCarrierTitle(): string
    {
        return $this->carrierTitle;
    }

    public function getTracking(): string
    {
        return $this->tracking;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
