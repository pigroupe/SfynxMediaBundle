<?php
namespace Sfynx\MediaBundle\Layers\Presentation\Coordination\Mediatheque\Query;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;

use Sfynx\ToolBundle\Builder\RouteTranslatorFactoryInterface;
use Sfynx\ToolBundle\Twig\Extension\PiToolExtension;
use Sfynx\ToolBundle\Twig\Extension\PiFormExtension;

use Sfynx\AuthBundle\Infrastructure\Role\Generalisation\RoleFactoryInterface;
use Sfynx\MediaBundle\Layers\Domain\Workflow\Observer\Mediatheque\Query\OBIndexCreateQueryHandler as OBMediathequeIndexCreateQueryHandler;
use Sfynx\MediaBundle\Layers\Domain\Workflow\Observer\Mediatheque\Query\OBIndexCreateJsonQueryHandler as OBMediathequeIndexCreateJsonQueryHandler;
use Sfynx\MediaBundle\Layers\Domain\Workflow\Observer\Mediatheque\Query\OBIndexFindEntitiesHandler as OBMediathequeIndexFindEntitiesHandler;
use Sfynx\MediaBundle\Layers\Domain\Workflow\Observer\Mediatheque\Response\OBCreateIndexBodyJson as OBMediathequeCreateIndexBodyJson;

use Sfynx\CoreBundle\Layers\Presentation\Coordination\Generalisation\AbstractQueryController;
use Sfynx\CoreBundle\Layers\Presentation\Adapter\Query\QueryAdapter;
use Sfynx\CoreBundle\Layers\Presentation\Request\Query\IndexQueryRequest;
use Sfynx\CoreBundle\Layers\Application\Query\IndexQuery;
use Sfynx\CoreBundle\Layers\Application\Common\Generalisation\Interfaces\HandlerInterface;
use Sfynx\CoreBundle\Layers\Application\Common\Handler\WorkflowHandler;
use Sfynx\CoreBundle\Layers\Application\Response\Handler\ResponseHandler;
use Sfynx\CoreBundle\Layers\Application\Response\Handler\Generalisation\Interfaces\ResponseHandlerInterface;
use Sfynx\CoreBundle\Layers\Application\Query\Handler\IndexQueryHandler;
use Sfynx\CoreBundle\Layers\Application\Query\Workflow\QueryWorkflow;
use Sfynx\CoreBundle\Layers\Domain\Workflow\Observer\Response\OBCreateResponseHtml;
use Sfynx\CoreBundle\Layers\Domain\Workflow\Observer\Response\OBCreateIndexBodyHtml;
use Sfynx\CoreBundle\Layers\Domain\Workflow\Observer\Response\OBCreateIndexBodyJson;
use Sfynx\CoreBundle\Layers\Domain\Workflow\Observer\Response\OBCreateIndexResponseJson;
use Sfynx\CoreBundle\Layers\Domain\Service\Manager\Generalisation\Interfaces\ManagerInterface;
use Sfynx\CoreBundle\Layers\Domain\Service\Request\Generalisation\RequestInterface;
use Sfynx\CoreBundle\Layers\Infrastructure\Exception\PresentationException;

/**
 * Index controller.
 *
 * @category   Sfynx\MediaBundle\Layers
 * @package    Presentation
 * @subpackage Coordination\Mediatheque\Query
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 */
class IndexController extends AbstractQueryController
{
    /** @var  ResponseHandlerInterface */
    protected $responseHandler;
    /** @var RoleFactoryInterface */
    protected $roleFactory;
    /** @var PiToolExtension */
    protected $toolExtension;
    /** @var RouteTranslatorFactoryInterface */
    protected $routeFactory;
    /** @var TranslatorInterface */
    protected $translator;

    /**
     * UsersController constructor.
     *
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param ManagerInterface $manager
     * @param RequestInterface $request
     * @param EngineInterface $templating
     * @param PiFormExtension $formExtension
     * @param RoleFactoryInterface $roleFactory
     * @param PiToolExtension $toolExtension
     * @param RouteTranslatorFactoryInterface $routeFactory
     * @param TranslatorInterface $translator
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        ManagerInterface $manager,
        RequestInterface $request,
        EngineInterface $templating,
        PiFormExtension $formExtension,
        RoleFactoryInterface $roleFactory,
        PiToolExtension $toolExtension,
        RouteTranslatorFactoryInterface $routeFactory,
        TranslatorInterface $translator
    ) {
        parent::__construct($authorizationChecker, $manager, $request, $templating, $formExtension);

        $this->roleFactory = $roleFactory;
        $this->routeFactory = $routeFactory;
        $this->toolExtension = $toolExtension;
        $this->translator = $translator;
    }

    /**
     * Lists all user entities.
     *
     * @return Response
     * @access public
     */
    public function coordinate()
    {
        if (false === $this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        // 1. Transform Request to Query.
        $adapter = new QueryAdapter(new IndexQuery());
        $query = $adapter->createQueryFromRequest(
            new IndexQueryRequest($this->request)
        );

        $query->isServerSide = true ;

        // 2. Implement the query workflow
        $Observer1 = new OBMediathequeIndexCreateQueryHandler($this->manager, $this->request);
        $Observer2 = new OBMediathequeIndexCreateJsonQueryHandler($this->manager, $this->request);
        $Observer3 = new OBMediathequeIndexFindEntitiesHandler(
            $this->manager,
            $this->request,
            $this->authorizationChecker,
            $this->roleFactory,
            $this->routeFactory
        );
        $workflowQuery = (new QueryWorkflow())
            ->attach($Observer1)
            ->attach($Observer2)
            ->attach($Observer3);

        // 3. Aapply the query workflow from the query
        $queryHandlerResult = (new IndexQueryHandler($workflowQuery))->process($query);
        if (!($queryHandlerResult instanceof HandlerInterface)) {
            throw PresentationException::invalidCommandHandlerResponse();
        }

        // 4. Implement the Response workflow
        $this->param->templating = 'SfynxMediaBundle:Mediatheque:index.html.twig';
        $Observer1 = new OBCreateIndexBodyHtml($this->request, $this->templating, $this->param);
        $Observer2 = new OBCreateResponseHtml($this->request);
        $Observer3 = new OBMediathequeCreateIndexBodyJson(
            $this->request,
            $this->roleFactory,
            $this->toolExtension,
            $this->routeFactory,
            $this->translator,
            $this->param
        );
        $Observer4 = new OBCreateIndexResponseJson($this->request);
        $workflowHandler = (new WorkflowHandler())
            ->attach($Observer1)
            ->attach($Observer2)
            ->attach($Observer3)
            ->attach($Observer4);

        // 5. Implement the responseHandler from the workflow
        $this->responseHandler = new ResponseHandler($workflowHandler);
        $responseHandlerResult = $this->responseHandler->process($queryHandlerResult);

        return $responseHandlerResult->response;
    }
}