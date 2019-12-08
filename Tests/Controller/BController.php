<?php

/*
 * This file is part of the Drift Twig Bundle
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

use React\Promise\FulfilledPromise;
use Drift\Twig\Controller\RenderableController;

/**
 * Class RenderableController.
 */
class BController implements RenderableController
{
    /**
     * Return value.
     *
     * @return array
     */
    public function __invoke(): array
    {
        return [
            'a' => '1',
            'b' => new FulfilledPromise('2'),
            'c' => [
                'd' => (new FulfilledPromise('3'))
                    ->then(function(string $value) {
                        return new FulfilledPromise($value);
                    }),
            ]
        ];
    }

    /**
     * Get render template
     *
     * @return string
     */
    public static function getTemplatePath(): string
    {
        return 'b.twig';
    }
}
