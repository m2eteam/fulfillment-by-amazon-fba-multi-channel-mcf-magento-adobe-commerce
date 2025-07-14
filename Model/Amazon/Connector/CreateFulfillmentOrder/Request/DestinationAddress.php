<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Amazon\Connector\CreateFulfillmentOrder\Request;

class DestinationAddress
{
    private string $name;
    private string $addressLine;
    private string $city;
    private string $phone;
    private string $stateOrRegion;
    private string $postalCode;
    private string $countryCode;

    public function __construct(
        string $name,
        string $addressLine,
        string $city,
        string $phone,
        string $stateOrRegion,
        string $postalCode,
        string $countryCode
    ) {
        $this->name = $name;
        $this->addressLine = $addressLine;
        $this->city = $city;
        $this->phone = $phone;
        $this->stateOrRegion = $stateOrRegion;
        $this->postalCode = $postalCode;
        $this->countryCode = $countryCode;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAddressLine(): string
    {
        return $this->addressLine;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getStateOrRegion(): string
    {
        return $this->stateOrRegion;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }
}
