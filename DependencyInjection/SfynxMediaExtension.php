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
        if (isset($config['mapping']['provider'])) {
            $container->setParameter('sfynx.media.mapping.provider', $config['mapping']['provider']);
        }

        if (isset($config['mapping']['media_class'])) {
            $container->setParameter('sfynx.media.media_class', $config['mapping']['media_class']);
        }
        if (isset($config['mapping']['media_entitymanager_command'])) {
            $container->setParameter('sfynx.media.media.entitymanager.command', $config['mapping']['media_entitymanager_command']);
        }
        if (isset($config['mapping']['media_entitymanager_query'])) {
            $container->setParameter('sfynx.media.media.entitymanager.query', $config['mapping']['media_entitymanager_query']);
        }
        if (isset($config['mapping']['media_entitymanager'])) {
            $container->setParameter('sfynx.media.media.entitymanager', $config['mapping']['media_entitymanager']);
        }

        if (isset($config['mapping']['mediatheque_class'])) {
            $container->setParameter('sfynx.media.mediatheque_class', $config['mapping']['mediatheque_class']);
        }
        if (isset($config['mapping']['mediatheque_entitymanager_command'])) {
            $container->setParameter('sfynx.media.mediatheque.entitymanager.command', $config['mapping']['mediatheque_entitymanager_command']);
        }
        if (isset($config['mapping']['mediatheque_entitymanager_query'])) {
            $container->setParameter('sfynx.media.mediatheque.entitymanager.query', $config['mapping']['mediatheque_entitymanager_query']);
        }
        if (isset($config['mapping']['mediatheque_entitymanager'])) {
            $container->setParameter('sfynx.media.mediatheque.entitymanager', $config['mapping']['mediatheque_entitymanager']);
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
        if (isset($config['cache_dir'])){
            if (isset($config['cache_dir']['media'])) {
                $container->setParameter('sfynx.media.cache_dir.media', $config['cache_dir']['media']);
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
