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

namespace Drift\Twig\Controller;

use React\Promise\PromiseInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Twig\Environment;
use function React\Promise\all;
use function React\Promise\resolve;

/**
 * Class ResponseTransformer.
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
     * Render view.
     *
     * @param ViewEvent $event
     *
     * @return PromiseInterface
     */
    public function renderView(ViewEvent $event): PromiseInterface
    {
        $templatePath = $this->getTemplatePathFromController($event);
        if (is_null($templatePath)) {
            return resolve($event->getControllerResult());
        }

        $controllerResult = $event->getControllerResult();
        $promisesReferences = [];
        $this->solvePromises($controllerResult, $promisesReferences);

        return all(array_column($promisesReferences, 'promise'))
            ->then(function (array $results) use (&$promisesReferences, $event, $controllerResult, $templatePath) {
                foreach ($promisesReferences as $key => &$result) {
                    $result['memory'] = $results[$key];
                }

                $template = $this
                    ->twig
                    ->load($templatePath);

                $event->setResponse(new Response(
                    $template->render($controllerResult),
                    200
                ));
            });
    }

    /**
     * Solve promises.
     *
     * @param array $controllerResult
     * @param array $promisesReferences
     */
    private function solvePromises(
        array &$controllerResult,
        array &$promisesReferences
    ) {
        foreach ($controllerResult as $key => &$element) {
            if ($element instanceof PromiseInterface) {
                $promisesReferences[] = [
                    'memory' => &$element,
                    'promise' => $element,
                ];

                continue;
            }

            if (is_array($element)) {
                $this->solvePromises($element, $promisesReferences);
            }
        }
    }

    /**
     * Get template path, or null if does not implement the interface.
     *
     * @param ViewEvent $event
     *
     * @return string|null
     */
    private function getTemplatePathFromController(ViewEvent $event): ? string
    {
        $controller = $event->getRequest()->attributes->get('_controller');
        $interfaces = class_implements($controller);

        return (array_key_exists(RenderableController::class, $interfaces))
            ? $controller::getTemplatePath()
            : null;
    }
}
