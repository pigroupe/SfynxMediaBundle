<?php
namespace Sfynx\MediaBundle\Layers\Presentation\Coordination\Mediatheque\Command;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface as LegacyValidatorInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\HttpFoundation\Response;

use Sfynx\ToolBundle\Builder\RouteTranslatorFactoryInterface;

/// TEST DDD
use Sfynx\CoreBundle\Layers\Presentation\Coordination\Generalisation\AbstractFormController;
use Sfynx\CoreBundle\Layers\Presentation\Adapter\Command\CommandAdapter;
use Sfynx\CoreBundle\Layers\Application\Response\Handler\Generalisation\Interfaces\ResponseHandlerInterface;
use Sfynx\CoreBundle\Layers\Application\Response\Handler\ResponseHandler;
use Sfynx\CoreBundle\Layers\Application\Common\Generalisation\Interfaces\HandlerInterface;
use Sfynx\CoreBundle\Layers\Application\Command\Handler\FormCommandHandler;
use Sfynx\CoreBundle\Layers\Application\Command\Handler\Decorator\CommandHandlerDecorator;
use Sfynx\CoreBundle\Layers\Application\Validation\Validator\SymfonyValidatorStrategy;
use Sfynx\CoreBundle\Layers\Application\Common\Handler\WorkflowHandler;
use Sfynx\CoreBundle\Layers\Application\Command\Workflow\CommandWorkflow;
use Sfynx\CoreBundle\Layers\Domain\Service\Manager\Generalisation\Interfaces\ManagerInterface;
use Sfynx\CoreBundle\Layers\Domain\Service\Request\Generalisation\RequestInterface;
use Sfynx\CoreBundle\Layers\Domain\Workflow\Observer\Response\OBCreateEntityFormView;
use Sfynx\CoreBundle\Layers\Domain\Workflow\Observer\Response\OBInjectFormErrors;
use Sfynx\CoreBundle\Layers\Domain\Workflow\Observer\Response\OBCreateFormBody;
use Sfynx\CoreBundle\Layers\Domain\Workflow\Observer\Response\OBCreateResponseHtml;
use Sfynx\CoreBundle\Layers\Infrastructure\Exception\WorkflowException;
use Sfynx\CoreBundle\Layers\Infrastructure\Exception\PresentationException;

use Sfynx\MediaBundle\Layers\Presentation\Request\Mediatheque\Command\FormRequest as MediathequeFormRequest;
use Sfynx\MediaBundle\Layers\Application\Cqrs\Mediatheque\Command\FormCommand as MediathequeFormCommand;
use Sfynx\MediaBundle\Layers\Application\Cqrs\Mediatheque\Command\Validation\SpecHandler\FormCommandSpecHandler as MediathequeFormCommandSpecHandler;
use Sfynx\MediaBundle\Layers\Application\Cqrs\Mediatheque\Command\Validation\ValidationHandler\FormCommandValidationHandler as MediathequeFormCommandValidationHandler;
use Sfynx\MediaBundle\Layers\Application\Validation\Type\MediathequeType;
use Sfynx\MediaBundle\Layers\Domain\Workflow\Observer\Mediatheque\Command\OBEntityEdit as OBMediathequeEntityEdit;
use Sfynx\MediaBundle\Layers\Domain\Workflow\Observer\Mediatheque\Command\OBEntityCreate as OBMediathequeEntityCreate;
use Sfynx\MediaBundle\Layers\Domain\Workflow\Observer\Mediatheque\Response\OBCreateFormData as OBMediathequeCreateFormData;

/**
 * class  FormController.
 *
 * @category   Sfynx\MediaBundle\Layers
 * @package    Presentation
 * @subpackage Coordination\Mediatheque\Command
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 */
class FormController extends AbstractFormController
{
    /** @var RouteTranslatorFactoryInterface */
    protected $routeFactory;

    /**
     * UsersController constructor.
     *
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param ManagerInterface $manager
     * @param ManagerInterface $managerGedmoCategory
     * @param RequestInterface $request
     * @param FormFactoryInterface $formFactory
     * @param EngineInterface $templating
     * @param LegacyValidatorInterface $validator
     * @param RouteTranslatorFactoryInterface $routeFactory
     * @param TranslatorInterface $translator
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        ManagerInterface $manager,
        ManagerInterface $managerGedmoCategory,
        RequestInterface $request,
        EngineInterface $templating,
        FormFactoryInterface $formFactory,
        LegacyValidatorInterface $validator,
        RouteTranslatorFactoryInterface $routeFactory,
        TranslatorInterface $translator
    ) {
        parent::__construct($authorizationChecker, $manager, $request, $templating, $formFactory, $validator, $translator);
        $this->managerGedmoCategory = $managerGedmoCategory;
        $this->routeFactory = $routeFactory;
    }

    /**
     * Displays a form to edit an existing user entity.
     *
     * @return Response
     * @access public
     */
    public function coordinate()
    {
        if (false === $this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        // 1. Transform Request to Command.
        $adapter = new CommandAdapter(new MediathequeFormCommand());
        $command = $adapter->createCommandFromRequest(
            new MediathequeFormRequest($this->request)
        );

        // 2. Implement the command workflow
        $Observer1 = new OBMediathequeEntityEdit($this->manager, $this->request);
        $Observer2 = new OBMediathequeEntityCreate($this->manager, $this->request, $this->routeFactory);
        $workflowCommand = (new CommandWorkflow())
            ->attach($Observer1)
            ->attach($Observer2);

        // 3. Implement decorator to apply the command workflow from the command
        $this->commandHandler = new FormCommandHandler($workflowCommand);
        $this->commandHandler = new MediathequeFormCommandValidationHandler(
            $this->commandHandler,
            new SymfonyValidatorStrategy($this->validator),
            false
        );
        $this->commandHandler = (new MediathequeFormCommandSpecHandler($this->commandHandler))->setObject(null);
        $commandHandlerResult = $this->commandHandler->process($command);
        if (!($commandHandlerResult instanceof HandlerInterface)) {
            throw PresentationException::invalidCommandHandlerResponse();
        }

        // 4. Implement the Response workflow
        $this->param->templating = 'SfynxMediaBundle:Mediatheque:edit.html.twig';
        $Observer1 = new OBMediathequeCreateFormData($this->request, $this->managerGedmoCategory);
        $Observer2 = new OBCreateEntityFormView($this->request, $this->formFactory, new MediathequeType(
            $this->manager,
            $this->routeFactory,
            $this->translator
        ));
        $Observer3 = new OBInjectFormErrors($this->request, $this->translator);
        $Observer4 = new OBCreateFormBody($this->request, $this->templating, $this->param);
        $Observer5 = new OBCreateResponseHtml($this->request);
        $workflowHandler = (new WorkflowHandler())
            ->attach($Observer1)
            ->attach($Observer2)
            ->attach($Observer3)
            ->attach($Observer4)
            ->attach($Observer5);

        // 5. Implement the responseHandler from the workflow
        $this->responseHandler = new ResponseHandler($workflowHandler);
        $responseHandlerResult = $this->responseHandler->process($commandHandlerResult);

//        print_r($responseHandlerResult->getResponse()->getTargetUrl());exit;

        return $responseHandlerResult->getResponse();
    }
}