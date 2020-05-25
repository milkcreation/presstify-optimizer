<?php declare(strict_types=1);

namespace tiFy\Plugins\Optimizer\Contracts;

use Psr\Container\ContainerInterface as Container;
use tiFy\Plugins\Optimizer\Contracts\Optimizer as OptimizerContract;

interface Optimizer
{
    /**
     * Initialisation du gestionnaire d'optimisation.
     *
     * @return static
     */
    public function boot(): Optimizer;

    /**
     * Récupération de l'instance du gestionnaire d'injection de dépendances.
     *
     * @return Container|null
     */
    public function getContainer(): ?Container;
}
