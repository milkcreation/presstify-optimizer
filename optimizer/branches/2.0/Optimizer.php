<?php declare(strict_types=1);

namespace tiFy\Plugins\Optimizer;

use Psr\Container\ContainerInterface as Container;
use tiFy\Plugins\Optimizer\Contracts\Optimizer as OptimizerContract;

/**
 * @desc Extension PresstiFy de recherche avancée.
 * @author Jordy Manner <jordy@milkcreation.fr>
 * @package tiFy\Plugins\Optimizer
 * @version 2.0.0
 *
 * USAGE :
 * Activation
 * ---------------------------------------------------------------------------------------------------------------------
 * Dans config/app.php ajouter \tiFy\Plugins\Optimizer\OptimizerServiceProvider à la liste des fournisseurs de services.
 * ex.
 * <?php
 *
 * return [
 *      ...
 *      'providers' => [
 *          ...
 *          tiFy\Plugins\Optimizer\OptimizerServiceProvider::class
 *          ...
 *      ]
 * ];
 *
 * Configuration
 * ---------------------------------------------------------------------------------------------------------------------
 * Dans le dossier de config, créer le fichier optimizer.php
 * @see Resources/config/optimizer.php
 */
class Optimizer implements OptimizerContract
{
    /**
     * Indicateur d'initialisation.
     * @var bool
     */
    protected $booted = false;

    /**
     * Instance du conteneur d'injection de dépendances.
     * @var Container|null
     */
    protected $container;

    /**
     * CONSTRUCTEUR.
     *
     * @param Container|null $container
     *
     * @return void
     */
    public function __construct(?Container $container = null)
    {
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function boot(): OptimizerContract
    {
        if (!$this->booted) {
            /* Modification des sources images pour activer le chargement différé */
            add_filter('wp_get_attachment_image_attributes', function ($attr, $attachment, $size) {
                if (is_admin()) {
                    return $attr;
                } elseif (!isset($attr['src'])) {
                    return $attr;
                } elseif (!in_array($size, ['archive', 'banner'])) {
                    return $attr;
                }

                $attr['data-src'] = $attr['src'];
                $attr['data-srcset'] = $attr['srcset'] ?? '';
                $attr['src'] = get_template_directory_uri() . "/dist/images/holder/{$size}.webp";
                $attr['srcset'] = '';

                return $attr;
            }, 10, 3);
            /**/

            /* Chargement différé des scripts */
            add_filter('script_loader_tag', function ($url) {
                if (is_admin()) {
                    return $url;
                } elseif (false === strpos($url, '.js')) {
                    return $url;
                } elseif (strpos($url, 'jquery.js')) {
                    return $url;
                }

                return str_replace(' src', ' async src', $url);
            });
            /**/

            $this->booted = true;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getContainer(): ?Container
    {
        return $this->container;
    }
}
