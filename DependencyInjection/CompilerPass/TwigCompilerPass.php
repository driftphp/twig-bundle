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

namespace Drift\Twig\DependencyInjection\CompilerPass;

use Drift\HttpKernel\AsyncKernelEvents;
use Drift\Twig\Controller\ResponseTransformer;
use Drift\Twig\Loader\InMemoryLoader;
use Drift\Twig\Loader\Precompiler;
use Drift\Twig\Loader\TemplateParser;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Loader\FilesystemLoader;

/**
 * Class TwigCompilerPass.
 */
class TwigCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $this->processTwigEngine($container);
        $this->processTwigEventListeners($container);
    }

    /**
     * Process Twig engine.
     *
     * @param ContainerBuilder $container
     */
    private function processTwigEngine(ContainerBuilder $container)
    {
        $viewsPath = $container->getParameter('twig.views_path');

        $container->setDefinition(
            'twig.template_parser',
            new Definition(TemplateParser::class, [
                [$viewsPath],
            ])
        );

        $container->setDefinition(
            'twig.array_loader',
            new Definition(ArrayLoader::class, [])
        );

        $container->setDefinition(
            'twig.filesystem_loader',
            new Definition(FilesystemLoader::class, [
                $viewsPath,
            ])
        );

        $container->setDefinition(
            'twig.in_memory_loader',
            (new Definition(InMemoryLoader::class, [
                new Reference('twig.array_loader'),
                new Reference('twig.template_parser'),
            ]))->addTag('kernel.event_listener', [
                'event' => AsyncKernelEvents::PRELOAD,
                'method' => 'preload',
            ])
        );

        $container->setDefinition(
            'twig.precompiler',
            (new Definition(Precompiler::class, [
                new Reference('twig'),
                new Reference('twig.template_parser'),
            ]))
            ->addTag('kernel.event_listener', [
                'event' => AsyncKernelEvents::PRELOAD,
                'method' => 'precompile',
            ])
        );

        $container->setDefinition(
            'twig',
            new Definition(Environment::class, [
                new Reference('twig.in_memory_loader'),
            ])
        );

        $container->setAlias(Environment::class, 'twig');
    }

    /**
     * Process Twig engine.
     *
     * @param ContainerBuilder $container
     */
    private function processTwigEventListeners(ContainerBuilder $container)
    {
        $container->setDefinition(
            'twig.response_transformer',
            (new Definition(ResponseTransformer::class, [
                new Reference('twig'),
            ]))
                ->addTag('kernel.event_listener', [
                    'event' => KernelEvents::VIEW,
                    'method' => 'renderView',
                ])
        );
    }
}
