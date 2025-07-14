<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Magento;

class RegionMapper
{
    private const COUNTRY_IDS_AMERICAN = [
        'BR', // Brazil
        'CA', // Canada
        'MX', // Mexico
        'US', // United States
    ];

    private const COUNTRY_IDS_EUROPEAN = [
        'AE', // United Arab Emirates
        'BE', // Belgium
        'DE', // Germany
        'ES', // Spain
        'FR', // France
        'GB', // United Kingdom
        'IN', // India
        'IT', // Italy
        'NL', // Netherlands
        'PL', // Poland
        'SA', // Saudi Arabia
        'SE', // Sweden
        'ZA', // South Africa
        'TR', // Turkey
    ];

    public static function getRegionByCountryId(string $countryId): string
    {
        if (in_array($countryId, self::COUNTRY_IDS_AMERICAN)) {
            return \M2E\AmazonMcf\Model\Account::REGION_AMERICA;
        }

        if (in_array($countryId, self::COUNTRY_IDS_EUROPEAN)) {
            return \M2E\AmazonMcf\Model\Account::REGION_EUROPE;
        }

        return \M2E\AmazonMcf\Model\Account::REGION_ASIA_PACIFIC;
    }
}
