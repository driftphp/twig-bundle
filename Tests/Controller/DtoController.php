<?php

declare(strict_types=1);

namespace Drift\Twig\Tests\Controller;

/**
 * Class NotATwigController
 */
class DtoController
{
    public function __invoke()
    {
        // just need anything I can run instanceof on...
        return new self;
    }
}
