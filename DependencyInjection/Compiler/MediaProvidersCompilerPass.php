<?php
namespace Sfynx\MediaBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;

/**
 * Class MediaProvidersCompilerPass
 *
 * @category   Sfynx\MediaBundle
 * @package    DependencyInjection
 * @subpackage Compiler
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2015 PI-GROUPE
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    2.3
 * @link       http://opensource.org/licenses/gpl-license.php
 * @since      2015-02-16
 */
class MediaProvidersCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $taggedServices = $container->findTaggedServiceIds(
            'sfynx_media.storage_provider'
        );

        $container->setParameter(
            'sfynx_media.config.storage_providers',
            $taggedServices
        );

        if ($container->hasDefinition('sfynx.media.event_subscriber.storage.handler')) {
            $definition = $container->getDefinition(
                'sfynx.media.event_subscriber.storage.handler'
            );

            foreach ($taggedServices as $id => $tagAttributes) {
                $providerDefinition = $container->getDefinition($id);
                $providerDefinition->addMethodCall('setName', [$id]);

                $definition->addMethodCall(
                    'addStorageProvider',
                    array(new Reference($id), $id)
                );
            }
        }
    }
}
