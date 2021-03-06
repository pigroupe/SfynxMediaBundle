<?php
/**
 * This file is part of the <Media> project.
 *
 * @category   SonataMedia
 * @package    DependencyInjection
 * @subpackage Configuration
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2015 PI-GROUPE
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    2.3
 * @link       http://opensource.org/licenses/gpl-license.php
 * @since      2015-02-16
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sfynx\MediaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 *
 * @category   SonataMedia
 * @package    DependencyInjection
 * @subpackage Configuration
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2015 PI-GROUPE
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    2.3
 * @link       http://opensource.org/licenses/gpl-license.php
 * @since      2015-02-16
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sfynx_media');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        \Sfynx\CoreBundle\DependencyInjection\Configuration::addMappingConfig($rootNode);
        $this->addStorageConfig($rootNode);
        $this->addCacheConfig($rootNode);
        $this->addMediaConfig($rootNode);
        $this->addFormatCreationConfig($rootNode);
        $this->addFormatsConfig($rootNode);
        $this->addCropConfig($rootNode);

        return $treeBuilder;
    }

    /**
     * Mapping config
     *
     * @param $rootNode \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     *
     * @return void
     * @access protected
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    protected function addStorageConfig(ArrayNodeDefinition $rootNode)
    {
        $rootNode
        ->children()
            ->arrayNode('storage')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('provider')->isRequired()->defaultValue('sfynx.media.storage_provider.api_media')->end()
                ->end()
            ->end()
        ->end();
    }

    /**
     * Cache config
     *
     * @param $rootNode ArrayNodeDefinition Class
     *
     * @return void
     * @access protected
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    protected function addCacheConfig(ArrayNodeDefinition $rootNode)
    {
        $rootNode
        ->children()
            ->arrayNode('cache_dir')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('media')->defaultValue('%kernel.root_dir%/cachesfynx/Media/')->cannotBeEmpty()->end()
                ->end()
            ->end()
        ->end();
    }

    /**
     * Media config
     *
     * @param $rootNode ArrayNodeDefinition Class
     *
     * @return void
     * @access protected
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    protected function addMediaConfig(ArrayNodeDefinition $rootNode)
    {
        $rootNode
        ->children()
            ->arrayNode('media')
                ->addDefaultsIfNotSet()
                ->children()
                    ->integerNode('quality')->defaultValue(95)->end()
                    ->arrayNode('token')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->integerNode('start')->defaultValue(0)->end()
                            ->integerNode('expire')->defaultValue(3600)->end()
                            ->booleanNode('unique')->defaultValue(false)->end()
                            ->arrayNode('ipRange')->prototype('scalar')->end()->defaultValue([])->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();
    }

    /**
     * Format creation config
     *
     * @param $rootNode \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     *
     * @return void
     * @access protected
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    protected function addFormatCreationConfig(ArrayNodeDefinition $rootNode)
    {
        $rootNode
        ->children()
            ->arrayNode('asynchrone_format_creation_options')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('parallel_limit')->defaultValue(3)->end()
                    ->scalarNode('curlopt_timeout_ms')->defaultValue(300)->end()
                    ->scalarNode('timeout_wait_response')->defaultValue(0.05)->end()
                ->end()
            ->end()
        ->end();
    }

    /**
     * Crop config
     *
     * @param ArrayNodeDefinition $rootNode An ArrayNodeDefinition instance
     *
     * @return void
     * @access protected
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    protected function addFormatsConfig(ArrayNodeDefinition $rootNode)
    {
        $rootNode
        ->children()
            ->arrayNode('formats')
            ->isRequired()
                ->prototype('array')
                ->children()
                    ->scalarNode('width')->defaultValue('')->end()
                    ->scalarNode('height')->defaultValue('')->end()
                    ->scalarNode('resize')->defaultValue(false)->end()
                    ->scalarNode('maxAge')->defaultValue(false)->end()
                    ->scalarNode('sharedMaxAge')->defaultValue('')->end()
                    ->scalarNode('scale')->defaultValue('')->end()
                    ->scalarNode('grayscale')->defaultValue('')->end()
                    ->scalarNode('rotate')->defaultValue('')->end()
                    ->scalarNode('maxwidth')->defaultValue('')->end()
                    ->scalarNode('maxheight')->defaultValue('')->end()
                    ->scalarNode('minwidth')->defaultValue('')->end()
                    ->scalarNode('minheight')->defaultValue('')->end()
                    ->scalarNode('noresponse')->defaultValue(false)->end()
                ->end()
            ->end()
        ->end();
    }

    /**
     * Crop config
     *
     * @param ArrayNodeDefinition $rootNode An ArrayNodeDefinition instance
     *
     * @return void
     * @access protected
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    protected function addCropConfig(ArrayNodeDefinition $rootNode)
    {
    	$rootNode
    	->children()
        	->arrayNode('crop')
                ->addDefaultsIfNotSet()
                ->children()
                        ->arrayNode('formats')
                        ->isRequired()
                            ->prototype('array')
                                ->children()
                                	->scalarNode('prefix')->cannotBeEmpty()->isRequired()->end()
                                    ->scalarNode('legend')->cannotBeEmpty()->isRequired()->end()
                                    ->scalarNode('width')->cannotBeEmpty()->isRequired()->end()
                                    ->scalarNode('height')->cannotBeEmpty()->isRequired()->end()
                                    ->scalarNode('ratio')->cannotBeEmpty()->isRequired()->end()
                                    ->scalarNode('quality')->cannotBeEmpty()->isRequired()->end()
                                ->end()
                            ->end()
                        ->end()
                ->end()
            ->end()
    	->end();
    }
}
