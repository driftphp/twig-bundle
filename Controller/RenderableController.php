<?php

namespace Drift\Twig\Controller\RenderableController;

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