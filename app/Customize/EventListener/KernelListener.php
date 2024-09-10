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

namespace Customize\EventListener;

use Eccube\Entity\Plugin;
use Eccube\Request\Context;
use Eccube\Service\SystemService;
use Eccube\Util\CacheUtil;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class KernelListener implements EventSubscriberInterface
{
    protected bool $isKernelException = false;

    protected CacheUtil $cacheUtil;

    protected Context $requestContext;

    protected SystemService $systemService;

    public function __construct(
        CacheUtil $cacheUtil,
        Context $requestContext,
        SystemService $systemService
    ) {
        $this->cacheUtil = $cacheUtil;
        $this->requestContext = $requestContext;
        $this->systemService = $systemService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
//            KernelEvents::EXCEPTION => [
//                ['onKernelException', 256],
//            ],
//            KernelEvents::TERMINATE => [
//                ['onKernelTerminate', -256],
//            ],
            KernelEvents::CONTROLLER_ARGUMENTS => [
                ['onKernelControllerArguments'],
            ],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $this->cacheUtil->clearCache();
        $this->isKernelException = true;
    }

    public function onKernelTerminate(TerminateEvent $event): void
    {
        if ($this->requestContext->isFront()) {
            return;
        }

        if (false === $this->isKernelException) {
            return;
        }

        log_info('プロクシ生成');

        $process = new Process(['bin/console', 'eccube:generate:proxies']);

        try {
            $process->mustRun(function ($type, $buffer) {
                if (Process::ERR === $type) {
                    log_error('ERR > ', [trim($buffer)]);
                }
            });
        } catch (ProcessFailedException $exception) {
            log_error('プロクシ生成失敗', [$exception->getMessage()]);
        }

        log_info('スキーマアップデート');

        $process = new Process(['bin/console', 'doctrine:schema:update', '--force']);

        try {
            $process->mustRun(function ($type, $buffer) {
                if (Process::ERR === $type) {
                    log_error('ERR > ', [$buffer]);
                }
            });
        } catch (ProcessFailedException $exception) {
            log_info('スキーマアップデート失敗', [$exception->getMessage()]);
        }

        $this->systemService->switchMaintenance();
    }

    public function onKernelControllerArguments(ControllerArgumentsEvent $event): void
    {
        if ($event->getRequest()->get('_route') !== 'admin_store_plugin_enable') {
            return;
        }

        $arguments = $event->getArguments();
        if (isset($arguments[0]) && $arguments[0] instanceof Plugin) {
            logs('enabled-plugin')->info('プラグイン有効化', ['ID' => $arguments[0]->getId(), 'Name' => $arguments[0]->getName()]);
        }
    }
}
