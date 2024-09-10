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

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\RemoteEvent\Attribute\AsRemoteEventConsumer;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;

#[AsRemoteEventConsumer(name: 'github')]
final readonly class GithubWebhookConsumer implements ConsumerInterface
{
    public function consume(RemoteEvent $event): void
    {
        $process = new Process(['git', 'pull']);
        try {
            $process->mustRun(function ($type, $buffer) {
                $buffer = trim($buffer);
                if (empty($buffer)) {
                    return;
                }
                if (Process::ERR === $type) {
                    log_error($buffer);
                } else {
                    log_info($buffer);
                }
                var_dump($buffer);
            });
        } catch (ProcessFailedException $exception) {
            var_dump($exception->getMessage());
            log_error($exception->getMessage());
        }
    }
}
