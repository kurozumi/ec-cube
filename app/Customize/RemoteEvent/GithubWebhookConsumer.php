<?php

/*
 * This file is part of EC-CUBE Demo
 *
 * Copyright(c) Akira Kurozumi All Rights Reserved.
 *
 * https://a-zumi.net
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
