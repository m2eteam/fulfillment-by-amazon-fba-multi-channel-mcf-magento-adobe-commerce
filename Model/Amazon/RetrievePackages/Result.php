<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Amazon\RetrievePackages;

class Result
{
    use \M2E\AmazonMcf\Model\Amazon\Connector\Message\MessageTrait;

    private const STATUS_PROCESSING = 'processing';
    private const STATUS_COMPLETE = 'complete';
    private const STATUS_INVALID = 'invalid';

    private string $status;
    /** @var Package[] */
    private array $packages = [];

    public static function createWithProcessingStatus(): self
    {
        return new self(self::STATUS_PROCESSING);
    }

    public static function createWithCompleteStatus(): self
    {
        return new self(self::STATUS_COMPLETE);
    }

    public static function createWithInvalidStatus(): self
    {
        return new self(self::STATUS_INVALID);
    }

    private function __construct(string $status)
    {
        $this->status = $status;
    }

    public function isStatusProcessing(): bool
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    public function isStatusCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETE;
    }

    public function isStatusInvalid(): bool
    {
        return $this->status === self::STATUS_INVALID;
    }

    public function addPackage(Package $package): self
    {
        $this->packages[] = $package;

        return $this;
    }

    /**
     * @return Package[]
     */
    public function getPackages(): array
    {
        return $this->packages;
    }
}
