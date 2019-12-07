<?php

namespace Drift\Twig\Controller;

/**
 * Class RenderableController
 */
interface RenderableController
{
    /**
     * Get render template
     *
     * @return string
     */
    public function getTemplatePath() : string;
}