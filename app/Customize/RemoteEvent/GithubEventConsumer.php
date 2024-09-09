<?php

namespace Customize\RemoteEvent;

use Symfony\Component\RemoteEvent\Attribute\AsRemoteEventConsumer;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;

#[AsRemoteEventConsumer(name: 'push')]
class GithubEventConsumer implements ConsumerInterface
{
    public function consume(RemoteEvent $event): void
    {
        $payload = $event->getPayload();
        log_info($payload['name'].':'.$payload['action']);
    }
}
