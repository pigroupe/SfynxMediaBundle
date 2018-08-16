<?php
namespace Sfynx\MediaBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

use Sfynx\CoreBundle\DependencyInjection\Compiler\Provider\FactoryPass;

/**
 * Class ChangeProviderPass
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
class ChangeProviderPass implements CompilerPassInterface
{
    /**
     * Processes the edition of the repository factory path depending of the DBMS to load.
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     * @throws InvalidArgumentException
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasParameter('sfynx.media.mapping.entities')) {
            foreach ($container->getParameter('sfynx.media.mapping.entities') as $entity => $parameters) {
                FactoryPass::create($entity, 'sfynx.media', $parameters, true)->process($container);
            }
        }
    }
}
