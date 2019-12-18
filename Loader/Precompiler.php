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

use Twig\Environment;

/**
 * Class Precompiler.
 */
class Precompiler
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var TemplateParser
     */
    private $templateParser;

    /**
     * Preloader constructor.
     *
     * @param Environment    $twig
     * @param TemplateParser $templateParser
     */
    public function __construct(
        Environment $twig,
        TemplateParser $templateParser
    ) {
        $this->twig = $twig;
        $this->templateParser = $templateParser;
    }

    /**
     * Preload.
     */
    public function precompile()
    {
        foreach ($this
                     ->templateParser
                     ->loadTemplates() as $templatePath => $content) {
            $this
                ->twig
                ->load($templatePath);
        }
    }
}
