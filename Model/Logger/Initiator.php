<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Logger;

class Initiator
{
    public const INITIATOR_UNKNOWN = 0;
    public const INITIATOR_USER = 1;
    public const INITIATOR_EXTENSION = 2;

    private static array $allInitiators = [
        self::INITIATOR_UNKNOWN,
        self::INITIATOR_USER,
        self::INITIATOR_EXTENSION,
    ];

    public static function isFamousInitiator(int $initiator): bool
    {
        return in_array($initiator, self::$allInitiators);
    }
}
