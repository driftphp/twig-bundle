<?php

namespace Drift\Twig\Loader;

use Symfony\Component\Finder\Finder;
use Twig\Error\LoaderError;
use Twig\Loader\ArrayLoader;
use Twig\Loader\LoaderInterface;
use Twig\Source;

/**
 * Class Preloader
 */
class Preloader implements LoaderInterface
{
    /**
     * @var ArrayLoader
     *
     * Loader
     */
    private $loader;
    private $paths;

    /**
     * Preloader constructor.
     *
     * @param ArrayLoader $loader
     * @param array $paths
     */
    public function __construct(
        ArrayLoader $loader,
        array $paths
    )
    {
        $this->loader = $loader;
        $this->paths = $paths;
    }

    /**
     * Pre load
     */
    public function preLoad()
    {
        $finder = new Finder();
        $finder
            ->in($this->paths)
            ->files()
            ->name('*.twig');

        foreach ($finder as $file) {
            $this
                ->loader
                ->setTemplate(
                    $file->getFilename(),
                    file_get_contents($file->getPath() . '/' . $file->getFilename())
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