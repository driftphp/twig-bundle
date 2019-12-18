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

use Symfony\Component\Finder\Finder;

/**
 * Class TemplateParser.
 */
class TemplateParser
{
    /**
     * @var string[]
     */
    private $templates = [];

    /**
     * @var array
     */
    private $paths;

    /**
     * Preloader constructor.
     *
     * @param array $paths
     */
    public function __construct(array $paths)
    {
        $this->paths = $paths;
    }

    /**
     * Load templates.
     */
    public function loadTemplates()
    {
        if (!empty($this->templates)) {
            return $this->templates;
        }

        $finder = new Finder();
        $finder
            ->in($this->paths)
            ->files()
            ->name('*.twig');

        foreach ($finder as $file) {
            $trimmedPaths = array_map(function (string $path) {
                return trim($path, '/');
            }, $this->paths);
            $relativePath = str_replace($trimmedPaths, '', $file->getPath());
            $relativePath = trim($relativePath, '/');
            $templateName = trim("$relativePath/".$file->getFilename(), '/');
            $templateContent = file_get_contents($file->getPath().'/'.$file->getFilename());

            $this->templates[$templateName] = $templateContent;
        }

        return $this->templates;
    }
}
