<?php

namespace M2E\AmazonMcf\Model\Amazon\Connector\Message;

trait MessageTrait
{
    /** @var \M2E\AmazonMcf\Model\Amazon\Connector\Message\Message[]  */
    private array $messages = [];

    public function setMessages(array $messages): self
    {
        $this->messages = $messages;

        return $this;
    }

    /**
     * @return \M2E\AmazonMcf\Model\Amazon\Connector\Message\Message[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }
}
