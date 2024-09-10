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

namespace Customize\Webhook;

use Symfony\Component\HttpFoundation\ChainRequestMatcher;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcher\HostRequestMatcher;
use Symfony\Component\HttpFoundation\RequestMatcher\IsJsonRequestMatcher;
use Symfony\Component\HttpFoundation\RequestMatcher\MethodRequestMatcher;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;
use Symfony\Component\Webhook\Client\AbstractRequestParser;
use Symfony\Component\Webhook\Exception\InvalidArgumentException;
use Symfony\Component\Webhook\Exception\RejectWebhookException;

final class GithubRequestParser extends AbstractRequestParser
{
    public const GIT_REF = 'refs/heads/demo';

    public function __construct(
        private readonly string $algo = 'sha256',
        private readonly string $signatureHeaderName = 'X-Hub-Signature-256',
        private readonly string $eventHeaderName = 'X-GitHub-Event',
        private readonly string $idHeaderName = 'X-GitHub-Hook-ID',
    )
    {
    }

    protected function getRequestMatcher(): RequestMatcherInterface
    {
        return new ChainRequestMatcher([
            new HostRequestMatcher('demo\.eccube-plugin\.net'),
            new IsJsonRequestMatcher(),
            new MethodRequestMatcher(Request::METHOD_POST),
        ]);
    }

    protected function doParse(Request $request, #[\SensitiveParameter] string $secret): ?RemoteEvent
    {
        if (!$secret) {
            throw new InvalidArgumentException('A non-empty secret is required.');
        }

        foreach ([$this->signatureHeaderName, $this->eventHeaderName, $this->idHeaderName] as $header) {
            if (!$request->headers->has($header)) {
                throw new RejectWebhookException(406, sprintf('Missing "%s" HTTP request signature header.', $header));
            }
        }

        // demoブランチではない場合はエラー
        $ref = $request->getPayload()->get('ref');
        if (self::GIT_REF !== $ref) {
            throw new RejectWebhookException(406, sprintf('Missing "%s".', $ref));
        }

        $this->validateSignature(
            headers: $request->headers,
            body: $request->getContent(),
            secret: $secret
        );
        return new RemoteEvent(
            name: $request->headers->get($this->eventHeaderName),
            id: $request->headers->get($this->idHeaderName),
            payload: $request->getPayload()->all()
        );
    }

    protected function validateSignature(HeaderBag $headers, string $body, #[\SensitiveParameter] string $secret): void
    {
        $signature = $headers->get($this->signatureHeaderName);
        if (!hash_equals($signature, $this->algo . '=' . hash_hmac($this->algo, $body, $secret))) {
            throw new RejectWebhookException(406, 'Signature is wrong.');
        }
    }
}
