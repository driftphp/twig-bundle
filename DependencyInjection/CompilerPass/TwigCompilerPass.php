<?php

namespace Drift\Twig\DependencyInjection\CompilerPass;

use Drift\Twig\Loader\Preloader;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Drift\Kernel;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Loader\FilesystemLoader;

/**
 * Class TwigCompilerPass
 */
class TwigCompilerPass implements CompilerPassInterface
{
    /**
     * @var Kernel
     */
    private $kernel;

    /**
     * TwigCompilerPass constructor.
     *
     * @param Kernel $kernel
     */
    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * You can modify the container here before it is dumped to PHP code.
     */
    public function process(ContainerBuilder $container)
    {
        $viewsPath = $this
            ->kernel
            ->getApplicationLayerDir() . '/views/';


        $container->setDefinition(
            'twig.array_loader',
            new Definition(ArrayLoader::class, [])
        );

        $container->setDefinition(
            'twig.filesystem_loader',
            new Definition(FilesystemLoader::class, [
                $viewsPath
            ])
        );

        $container->setDefinition(
            'twig.preloader',
            new Definition(Preloader::class, [
                new Reference('twig.array_loader'),
                [$viewsPath]
            ])
        );

        $container->setDefinition(
            'twig',
            new Definition(Environment::class, [
                new Reference('twig.preloader')
            ])
        );

        $container->setAlias(Environment::class, 'twig');
    }
}