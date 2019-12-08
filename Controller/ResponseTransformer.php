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

namespace Drift\Twig\Controller;

use React\Promise\PromiseInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Twig\Environment;
use React\Promise;

/**
 * Class ResponseTransformer
 */
class ResponseTransformer
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * PutValueController constructor.
     *
     * @param Environment $twig
     */
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * Render view
     *
     * @param ViewEvent $event
     *
     * @return PromiseInterface
     */
    public function renderView(ViewEvent $event) : PromiseInterface
    {
        $controllerResult = $event->getControllerResult();
        $promisesReferences = [];
        $this->solvePromises($controllerResult, $promisesReferences);

        return Promise\all(array_column($promisesReferences, 'promise'))
            ->then(function(array $results) use (&$promisesReferences, $event, $controllerResult) {
                foreach ($promisesReferences as $key => &$result) {
                    $result['memory'] = $results[$key];
                }

                $this->quessAndRenderView(
                    $event,
                    $controllerResult
                );
            });
    }

    /**
     * Solve promises
     *
     * @param array $controllerResult
     * @param array $promisesReferences
     */
    private function solvePromises(
        array &$controllerResult,
        array &$promisesReferences
    )
    {
        foreach ($controllerResult as $key => &$element) {
            if ($element instanceof PromiseInterface) {
                $promisesReferences[] = [
                    'memory' => &$element,
                    'promise' => $element
                ];

                continue;
            }

            if (is_array($element)) {
                $this->solvePromises($element, $promisesReferences);
            }
        }
    }

    /**
     * Render view
     *
     * @param ViewEvent $event
     * @param array $data
     */
    private function quessAndRenderView(
        ViewEvent $event,
        array $data
    )
    {
        $controller = $event->getRequest()->attributes->get('_controller');
        $interfaces = class_implements($controller);

        if (array_key_exists(RenderableController::class, $interfaces)) {
            $templatePath = $controller::getTemplatePath();

            $template = $this->twig->load($templatePath);

            $event->setResponse(new Response(
                $template->render($data),
                200
            ));
        }
    }
}