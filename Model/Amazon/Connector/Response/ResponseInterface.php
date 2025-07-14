<?php

namespace M2E\AmazonMcf\Model\Amazon\Connector\Response;

interface ResponseInterface
{
    public function setMessages(array $messages): self;

    /**
     * @return \M2E\AmazonMcf\Model\Amazon\Connector\Message\Message[]
     */
    public function getMessages(): array;
}
