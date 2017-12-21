<?php
namespace Sfynx\MediaBundle\Layers\Domain\Workflow\Observer\Mediatheque\Command;

use Exception;
use Symfony\Component\Form\Extension\Core\DataTransformer\ValueToDuplicatesTransformer;

use Sfynx\ToolBundle\Builder\RouteTranslatorFactoryInterface;
use Sfynx\MediaBundle\Layers\Domain\Generalisation\Interfaces\UserInterface;
use Sfynx\MediaBundle\Layers\Domain\Specification\SpecIsCommandCreatedWithStatus;
use Sfynx\MediaBundle\Layers\Domain\Specification\SpecIsCommandCreatedWithCategory;
use Sfynx\MediaBundle\Layers\Domain\Specification\SpecIsCommandCreatedWithNoLayout;
use Sfynx\CoreBundle\Layers\Domain\Service\Manager\Generalisation\Interfaces\ManagerInterface;
use Sfynx\CoreBundle\Layers\Domain\Service\Request\Generalisation\RequestInterface;
use Sfynx\CoreBundle\Layers\Domain\Workflow\Observer\Generalisation\Command\AbstractEntityEditHandler;
use Sfynx\CoreBundle\Layers\Infrastructure\Exception\EntityException;


/**
 * Class OBEntityEdit
 *
 * @category Sfynx\MediaBundle\Layers
 * @package Domain
 * @subpackage Workflow\Observer\Mediatheque\Command
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2016 PI-GROUPE
 */
class OBEntityEdit extends AbstractEntityEditHandler
{
    use TraitProcess;
}
