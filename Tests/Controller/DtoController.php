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

namespace Drift\Twig\Tests\Controller;

/**
 * Class NotATwigController.
 */
class DtoController
{
    public function __invoke()
    {
        // just need anything I can run instanceof on...
        return new self();
    }
}
