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

namespace Drift\Twig\Preset;

use Drift\Preload\Preset\Preset;

/**
 * Class TwigPreset.
 */
class TwigPreset implements Preset
{
    /**
     * Get services to preload.
     *
     * Return an array of service and method to preload.
     * Null if with the controller is enough
     */
    public static function getServicesToPreload(): array
    {
        return [
            'twig' => null,
        ];
    }

    /**
     * Get namespaces to preload.
     *
     * Return an array of namespaces to preload
     *
     * @param string $projectDir
     *
     * @return array
     */
    public static function getNamespacesToPreload(string $projectDir): array
    {
        return [];
    }
}
