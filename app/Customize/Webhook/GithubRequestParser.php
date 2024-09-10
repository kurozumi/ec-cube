<?php
declare(strict_types=1);

namespace Customize\Webhook;

use Symfony\Component\HttpFoundation\ChainRequestMatcher;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcher\HostRequestMatcher;
use Symfony\Component\HttpFoundation\RequestMatcher\IsJsonRequestMatcher;
use Symfony\Component\HttpFoundation\RequestMatcher\MethodRequestMatcher;
use Symfony\Component\HttpFoundation\RequestMatcher\PathRequestMatcher;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;
use Symfony\Component\Webhook\Client\AbstractRequestParser;
use Symfony\Component\Webhook\Exception\RejectWebhookException;

final class GithubRequestParser extends AbstractRequestParser
{
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
            new HostRequestMatcher('github\.com'),
            new IsJsonRequestMatcher(),
            new MethodRequestMatcher(Request::METHOD_POST),
        ]);
    }

    protected function doParse(Request $request, #[\SensitiveParameter] string $secret): ?RemoteEvent
    {
        var_dump($request->getHost());
        exit();
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

    protected function validate(Request $request): void
    {
        if (!$this->getRequestMatcher()->matches($request)) {
            throw new RejectWebhookException(406, 'Request does not match.');
        }
    }

    protected function validateSignature(HeaderBag $headers, string $body, #[\SensitiveParameter] string $secret): void
    {
        $signature = $headers->get($this->signatureHeaderName);
        $event = $headers->get($this->eventHeaderName);
        $id = $headers->get($this->idHeaderName);

        if (!hash_equals($signature, $this->algo.'='.hash_hmac($this->algo, $event.$id.$body, $secret))) {
            throw new RejectWebhookException(406, 'Signature is wrong.');
        }
    }
}
