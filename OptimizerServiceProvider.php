<?php declare(strict_types=1);

namespace tiFy\Plugins\Optimizer;

use tiFy\Container\ServiceProvider;

class OptimizerServiceProvider extends ServiceProvider
{
    /**
     * Liste des noms de qualification des services fournis.
     * {@internal Permet le chargement différé des services qualifié.}
     * @var string[]
     */
    protected $provides = [
        'optimizer',
    ];

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        events()->listen('wp.booted', function () {
            $this->getContainer()->get('optimizer')->boot();
        });
    }

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share('optimizer', function () {
            return new Optimizer(config('optimizer', []), $this->getContainer());
        });
    }
}