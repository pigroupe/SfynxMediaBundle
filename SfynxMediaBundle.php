<?php
namespace Sfynx\MediaBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Sfynx\MediaBundle\DependencyInjection\Compiler\ChangeProviderPass;
use Sfynx\MediaBundle\DependencyInjection\Compiler\PiTwigEnvironmentPass;
use Sfynx\MediaBundle\DependencyInjection\Compiler\MediaProvidersCompilerPass;

/**
 * Media bundle
 *
 * @category   Sfynx\MediaBundle
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2015 PI-GROUPE
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    2.3
 * @link       http://opensource.org/licenses/gpl-license.php
 * @since      2015-02-16
 */
class SfynxMediaBundle extends Bundle
{
    /**
     * Builds the bundle.
     *
     * It is only ever called once when the cache is empty.
     *
     * This method can be overridden to register compilation passes,
     * other extensions, ...
     *
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        // register extension
        $container->addCompilerPass(new ChangeProviderPass());
        $container->addCompilerPass(new PiTwigEnvironmentPass());
        $container->addCompilerPass(new MediaProvidersCompilerPass());
        $container->setParameter('kernel.http_host', '');
    }

    /**
     * Boots the Bundle.
     */
    public function boot()
    {
    }

    /**
     * Shutdowns the Bundle.
     */
    public function shutdown()
    {
    }
}
