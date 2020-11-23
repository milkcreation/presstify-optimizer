<?php declare(strict_types=1);

namespace tiFy\Plugins\Optimizer\Contracts;

use Exception;
use Psr\Container\ContainerInterface as Container;
use tiFy\Contracts\Support\ParamsBag;

interface Optimizer
{
    /**
     * Récupération de l'instance de l'extension gestion d'optimisation.
     *
     * @return static
     *
     * @throws Exception
     */
    public static function instance(): Optimizer;

    /**
     * Initialisation du gestionnaire d'optimisation.
     *
     * @return static
     */
    public function boot(): Optimizer;

    /**
     * Récupération de paramètre|Définition de paramètres|Instance du gestionnaire de paramètre.
     *
     * @param string|array|null $key Clé d'indice du paramètre à récupérer|Liste des paramètre à définir.
     * @param mixed $default Valeur de retour par défaut lorsque la clé d'indice est une chaine de caractère.
     *
     * @return mixed|ParamsBag
     */
    public function config($key = null, $default = null);

    /**
     * Récupération de l'instance du gestionnaire d'injection de dépendances.
     *
     * @return Container|null
     */
    public function getContainer(): ?Container;

    /**
     * Chemin absolu vers une ressources (fichier|répertoire).
     *
     * @param string $path Chemin relatif vers la ressource.
     *
     * @return string
     */
    public function resources(string $path = ''): string;

    /**
     * Définition des paramètres de configuration.
     *
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return static
     */
    public function setConfig(array $attrs): Optimizer;

    /**
     * Définition du conteneur d'injection de dépendances.
     *
     * @param Container $container
     *
     * @return static
     */
    public function setContainer(Container $container): Optimizer;
}
