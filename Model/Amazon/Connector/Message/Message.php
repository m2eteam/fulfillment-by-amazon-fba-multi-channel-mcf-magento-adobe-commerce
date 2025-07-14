<?php

declare(strict_types=1);

namespace M2E\AmazonMcf\Model\Amazon\Connector\Message;

class Message
{
    private string $text;

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    public function getText(): string
    {
        return $this->text;
    }
}
