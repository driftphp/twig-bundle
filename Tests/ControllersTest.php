<?php

/*
 * This file is part of the DriftPHP Project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */

declare(strict_types=1);

namespace Drift\Twig\Tests;

use Clue\React\Block;
use Drift\Twig\Tests\Controller\DtoController;
use React\EventLoop\StreamSelectLoop;
use React\Promise;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class ControllersTest.
 */
class ControllersTest extends TwigBundleFunctionalTest
{
    /**
     * Test controllers.
     */
    public function testControllers()
    {
        self::$kernel->preload();
        $loop = new StreamSelectLoop();

        /*
         * Renaming the file should not have effect here, so the file is already
         * loaded by the Loader.
         */
        rename(__DIR__.'/views/a.twig', __DIR__.'/views/a.twig');

        $promise1 = self::$kernel
            ->handleAsync(new Request([], [], [], [], [], [
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/a',
                'SERVER_PORT' => 80,
            ]))
            ->then(function (Response $response) {
                $this->assertEquals(
                    '>>~~A~~<<',
                    $response->getContent()
                );
            });

        $promise2 = self::$kernel
            ->handleAsync(new Request([], [], [], [], [], [
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/b',
                'SERVER_PORT' => 80,
            ]))
            ->then(function (Response $response) {
                $this->assertEquals(
                    '1~~2~~3',
                    $response->getContent()
                );
            });

        $loop->run();
        Block\await(
            Promise\all([
                $promise1,
                $promise2,
            ]), $loop
        );

        rename(__DIR__.'/views/a.twig', __DIR__.'/views/a.twig');
    }

    public function testControllerResultNotIntendedToBeRendered(): void
    {
        $dtoResponse = new Response("I'm the response");
        $listener = static function (ViewEvent $event) use ($dtoResponse) {
            return (new Promise\FulfilledPromise())
                ->then(
                    static function () use ($dtoResponse, $event) {
                        if ($event->getControllerResult() instanceof DtoController) {
                            $event->setResponse($dtoResponse);
                        }
                    }
                );
        };
        /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = self::$kernel->getContainer()->get('event_dispatcher');
        $eventDispatcher->addListener(KernelEvents::VIEW,$listener);

        self::$kernel->preload();
        $loop = new StreamSelectLoop();

        $promise1 = self::$kernel
            ->handleAsync(
                new Request(
                    [], [], [], [], [], [
                    'REQUEST_METHOD' => 'GET',
                    'REQUEST_URI'    => '/dto',
                    'SERVER_PORT'    => 80,
                ]
                )
            )
            ->then(
                static function (Response $response) use ($dtoResponse) {
                    self::assertSame($dtoResponse, $response);
                }
            );

        $loop->run();
        Block\await($promise1, $loop);
    }
}
