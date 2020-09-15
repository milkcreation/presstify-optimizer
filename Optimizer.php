<?php declare(strict_types=1);

namespace tiFy\Plugins\Optimizer;

use Exception;
use Psr\Container\ContainerInterface as Container;
use tiFy\Plugins\Optimizer\Contracts\Optimizer as OptimizerContract;
use tiFy\Support\ParamsBag;

/**
 * @desc Extension PresstiFy de recherche avancée.
 * @author Jordy Manner <jordy@milkcreation.fr>
 * @package tiFy\Plugins\Optimizer
 * @version 2.0.1
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
     * Instance de l'extension de gestion d'optimisation de site.
     * @var OptimizerContract|null
     */
    protected static $instance;

    /**
     * Indicateur d'initialisation.
     * @var bool
     */
    protected $booted = false;

    /**
     * Instance de la configuration associée.
     * @var ParamsBag|null
     */
    protected $config;

    /**
     * Instance du conteneur d'injection de dépendances.
     * @var Container|null
     */
    protected $container;

    /**
     * CONSTRUCTEUR.
     *
     * @param array $config
     * @param Container|null $container
     *
     * @return void
     */
    public function __construct(array $config = [], ?Container $container = null)
    {
        $this->setConfig($config);

        if (!is_null($container)) {
            $this->setContainer($container);
        }

        if (!self::$instance instanceof OptimizerContract) {
            self::$instance = $this;
        }
    }

    /**
     * @inheritDoc
     */
    public static function instance(): OptimizerContract
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }

        throw new Exception(__('Impossible de récupérer l\'instance du gestionnaire d\'optimisation.', 'tify'));
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
                } elseif (!$sizes = $this->config('defer.img.sizes')) {
                    return $attr;
                } elseif (!in_array($size, array_keys($sizes))) {
                    return $attr;
                }

                $attr['data-src'] = $attr['src'];
                $attr['data-srcset'] = $attr['srcset'] ?? '';
                $attr['src'] = $sizes[$size]['placeholder'] ?? '';
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
    public function config($key = null, $default = null)
    {
        if (is_null($this->config)) {
            $this->config = new ParamsBag();
        }

        if (is_string($key)) {
            return $this->config->get($key, $default);
        } elseif (is_array($key)) {
            return $this->config->set($key);
        } else {
            return $this->config;
        }
    }

    /**
     * @inheritDoc
     */
    public function getContainer(): ?Container
    {
        return $this->container;
    }

    /**
     * @inheritDoc
     */
    public function resources($path = ''): string
    {
        $path = $path ? '/' . ltrim($path, '/') : '';

        return file_exists(__DIR__ . "/Resources{$path}") ? __DIR__ . "/Resources{$path}" : '';
    }

    /**
     * @inheritDoc
     */
    public function setConfig(array $attrs): OptimizerContract
    {
        $this->config($attrs);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setContainer(Container $container): OptimizerContract
    {
        $this->container = $container;

        return $this;
    }
}
