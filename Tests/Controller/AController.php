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

use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

/**
 * Class Controller.
 */
class AController
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * Controller constructor.
     *
     * @param Environment $twig
     */
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * Return value.
     *
     * @return Response
     */
    public function __invoke(): Response
    {
        return new Response(
            $this
                ->twig
                ->render('a.twig', ['value' => 'A'])
        );
    }
}
