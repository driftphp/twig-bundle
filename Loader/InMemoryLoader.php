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

namespace Drift\Twig\Loader;

use Twig\Error\LoaderError;
use Twig\Loader\ArrayLoader;
use Twig\Loader\LoaderInterface;
use Twig\Source;

/**
 * Class Preloader.
 */
class InMemoryLoader implements LoaderInterface
{
    /**
     * @var ArrayLoader
     *
     * Loader
     */
    private $loader;

    /**
     * @var TemplateParser
     */
    private $templateParser;

    /**
     * Preloader constructor.
     *
     * @param ArrayLoader    $loader
     * @param TemplateParser $templateParser
     */
    public function __construct(
        ArrayLoader $loader,
        TemplateParser $templateParser
    ) {
        $this->loader = $loader;
        $this->templateParser = $templateParser;
    }

    /**
     * Preload.
     */
    public function preload()
    {
        foreach ($this
            ->templateParser
            ->loadTemplates() as $templatePath => $content) {
            $this
                ->loader
                ->setTemplate(
                    $templatePath,
                    $content
                );
        }
    }

    /**
     * Returns the source context for a given template logical name.
     *
     * @throws LoaderError When $name is not found
     */
    public function getSourceContext(string $name): Source
    {
        return $this->loader->getSourceContext($name);
    }

    /**
     * Gets the cache key to use for the cache for a given template name.
     *
     * @throws LoaderError When $name is not found
     */
    public function getCacheKey(string $name): string
    {
        return  $this->loader->getCacheKey($name);
    }

    /**
     * @param int $time Timestamp of the last modification time of the cached template
     *
     * @throws LoaderError When $name is not found
     */
    public function isFresh(string $name, int $time): bool
    {
        return $this->loader->isFresh($name, $time);
    }

    /**
     * @return bool
     */
    public function exists(string $name)
    {
        $this->loader->exists($name);
    }
}
