<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Amazon\Connector\GetPackageDetails;

class Response implements \M2E\AmazonMcf\Model\Amazon\Connector\Response\ResponseInterface
{
    use \M2E\AmazonMcf\Model\Amazon\Connector\Message\MessageTrait;

    private ?string $trackingNumber = null;
    private ?string $carrierCode = null;
    private ?string $carrierUrl = null;

    public function retrieveTrackingNumber(): ?string
    {
        return $this->trackingNumber;
    }

    public function setTrackingNumber(string $trackingNumber): void
    {
        $this->trackingNumber = $trackingNumber;
    }

    public function retrieveCarrierCode(): ?string
    {
        return $this->carrierCode;
    }

    public function setCarrierCode(string $carrierCode): void
    {
        $this->carrierCode = $carrierCode;
    }

    public function retrieveCarrierUrl(): ?string
    {
        return $this->carrierUrl;
    }

    public function setCarrierUrl(string $carrierUrl): void
    {
        $this->carrierUrl = $carrierUrl;
    }
}
