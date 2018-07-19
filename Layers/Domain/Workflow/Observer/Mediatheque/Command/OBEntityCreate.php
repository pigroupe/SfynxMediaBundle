<?php
namespace Sfynx\MediaBundle\Layers\Domain\Workflow\Observer\Mediatheque\Command;

use Exception;
use Symfony\Component\Form\Extension\Core\DataTransformer\ValueToDuplicatesTransformer;

use Sfynx\ToolBundle\Builder\RouteTranslatorFactoryInterface;
use Sfynx\CoreBundle\Layers\Domain\Service\Manager\Generalisation\Interfaces\ManagerInterface;
use Sfynx\CoreBundle\Layers\Domain\Service\Request\Generalisation\RequestInterface;
use Sfynx\CoreBundle\Layers\Domain\Workflow\Observer\Generalisation\Command\AbstractEntityCreateHandler;
use Sfynx\CoreBundle\Layers\Infrastructure\Exception\EntityException;
use Sfynx\MediaBundle\Layers\Domain\Generalisation\Interfaces\UserInterface;
use Sfynx\MediaBundle\Layers\Domain\Specification\SpecIsCommandCreatedWithStatus;
use Sfynx\MediaBundle\Layers\Domain\Specification\SpecIsCommandCreatedWithCategory;
use Sfynx\MediaBundle\Layers\Domain\Specification\SpecIsCommandCreatedWithNoLayout;

/**
 * Class OBEntityCreate
 *
 * @category Sfynx\MediaBundle\Layers
 * @package Domain
 * @subpackage Workflow\Observer\Mediatheque\Command
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2016 PI-GROUPE
 */
class OBEntityCreate extends AbstractEntityCreateHandler
{
    use TraitProcess;

    /** @var RouteTranslatorFactoryInterface */
    protected $router;

    /**
     * AbstractEntityCreateHandler constructor.
     * @param ManagerInterface $manager
     * @param RequestInterface $request
     * @param RouteTranslatorFactoryInterface $router
     */
    public function __construct(ManagerInterface $manager, RequestInterface $request, RouteTranslatorFactoryInterface $router, bool $updateCommand = false)
    {
        parent::__construct($manager, $request, $updateCommand);
        $this->router = $router;
    }


    /**
     * {@inheritdoc}
     */
    protected function onEnd(): void
    {
        $object = new \StdClass();
        $object->wfCommand = $this->wfCommand;

        $specs = (new SpecIsCommandCreatedWithStatus())
            ->AndSpec(new SpecIsCommandCreatedWithCategory())
            ->AndSpec(new SpecIsCommandCreatedWithNoLayout())
        ;
        if ($specs->isSatisfiedBy($object) && null !== ($this->wfLastData->entity->getId())) {
            $this->wfLastData->url = $this->router->generate('sfynx_media_mediatheque_edit', [
                'id' => $this->wfLastData->entity->getId(),
                'NoLayout' => $this->wfCommand->getNoLayout(),
                'category' => $this->wfCommand->getCategory(),
                'status' => $this->wfCommand->getStatus()
            ]);
        }
    }
}
