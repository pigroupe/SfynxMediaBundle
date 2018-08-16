<?php
namespace Sfynx\MediaBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension,
    Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\Loader,
    Symfony\Component\Config\FileLocator;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * @category   SonataMedia
 * @package    DependencyInjection
 * @subpackage Extension
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2015 PI-GROUPE
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    2.3
 * @link       http://opensource.org/licenses/gpl-license.php
 * @since      2015-02-16
 */
class SfynxMediaExtension extends Extension
{
    /**
     * @param array $config
     * @param ContainerBuilder $container
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loaderYaml = new Loader\YamlFileLoader($container, new FileLocator(realpath(__DIR__ . '/../Resources/config')));
        $loaderYaml->load('service/services_type.yml');
        $loaderYaml->load('service/services_handler.yml');
        $loaderYaml->load('service/services_twig_extension.yml');

        $loaderYaml->load('repository/mediatheque.yml');
        $loaderYaml->load('controller/mediatheque/mediatheque_command.yml');
        $loaderYaml->load('controller/mediatheque/mediatheque_query.yml');

        $loaderYaml->load('repository/media.yml');

        // we load config
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $config);

        /*
         * Mapping config parameter
         */
        if (isset($config['mapping']['entities'])) {
            $container->setParameter("sfynx.media.mapping.entities", $config['mapping']['entities']);
            foreach ($config['mapping']['entities'] as $entity => $param) {
                $container->setParameter("sfynx.media.mapping.{$entity}.class", $param['class']);
            }
        }

        /**
         * Storage config parameter
         */
        if (isset($config['storage']['provider'])) {
            $container->setParameter('sfynx.media.storage.provider', $config['storage']['provider']);
        }

        /**
         * Cache config parameter
         */
        if (isset($config['cache_dir'])) {
            if (isset($config['cache_dir']['media'])) {
                $container->setParameter('sfynx.media.cache_dir.media', $config['cache_dir']['media']);
            }
        }

        /**
         * Media config parameter
         */
        if (isset($config['media'])) {
            if (isset($config['media']['quality'])) {
                $container->setParameter('sfynx.media.quality', $config['media']['quality']);
            }
            if (isset($config['media']['token'])) {
                $container->setParameter('sfynx.media.token', $config['media']['token']);
            }
        }

        /**
         * Formats creation config parameter
         */
        if (isset($config['asynchrone_format_creation_options'])) {
            $container->setParameter("sfynx.media.format_creation", $config['asynchrone_format_creation_options']);
            foreach ($config['asynchrone_format_creation_options'] as $name => $parameters) {
                $container->setParameter("sfynx.media.format_creation.{$name}", $parameters);
            }
        }

        /**
         * Formats config parameter
         */
        if (isset($config['formats'])) {
            $container->setParameter("sfynx.media.formats", $config['formats']);
            foreach ($config['formats'] as $name => $parameters) {
                $container->setParameter("sfynx.media.format.{$name}", $parameters);
            }
        }

        /**
         * Crop config parameter
         */
        if (isset($config['crop'])) {
            $container->setParameter('sfynx.media.crop', $config['crop']);
            if (isset($config['crop']['formats'])) {
                $container->setParameter('sfynx.media.crop.formats', $config['crop']['formats']);
            }
        }
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return 'sfynx_media';
    }
}
