<?php

namespace Drift\Twig;

use Drift\Kernel;
use Drift\Twig\DependencyInjection\CompilerPass\TwigCompilerPass;
use Mmoreram\BaseBundle\BaseBundle;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Class TwigBundle
 */
class TwigBundle extends BaseBundle
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
     * Return a CompilerPass instance array.
     *
     * @return CompilerPassInterface[]
     */
    public function getCompilerPasses(): array
    {
        return [
            new TwigCompilerPass($this->kernel)
        ];
    }
}