<?php
declare(strict_types=1);

namespace Customize\RemoteEvent;

use Symfony\Component\RemoteEvent\Attribute\AsRemoteEventConsumer;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;

#[AsRemoteEventConsumer(name: 'github')]
final readonly class GithubWebhookConsumer implements ConsumerInterface
{
    public function consume(RemoteEvent $event): void
    {
        $payload = $event->getPayload();
        log_info($event->getName().':'.implode(',', $payload));
    }
}
