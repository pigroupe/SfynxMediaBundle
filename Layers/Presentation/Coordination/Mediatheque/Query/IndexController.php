<?php
namespace Sfynx\MediaBundle\Layers\Presentation\Coordination\Mediatheque\Query;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;

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

use Sfynx\ToolBundle\Builder\RouteTranslatorFactoryInterface;
use Sfynx\ToolBundle\Twig\Extension\PiToolExtension;
use Sfynx\ToolBundle\Twig\Extension\PiFormExtension;

use Sfynx\AuthBundle\Infrastructure\Role\Generalisation\RoleFactoryInterface;
use Sfynx\MediaBundle\Layers\Domain\Workflow\Observer\Mediatheque\Query\OBIndexCreateQueryHandler as OBMediathequeIndexCreateQueryHandler;
use Sfynx\MediaBundle\Layers\Domain\Workflow\Observer\Mediatheque\Query\OBIndexCreateJsonQueryHandler as OBMediathequeIndexCreateJsonQueryHandler;
use Sfynx\MediaBundle\Layers\Domain\Workflow\Observer\Mediatheque\Query\OBIndexFindEntitiesHandler as OBMediathequeIndexFindEntitiesHandler;
use Sfynx\MediaBundle\Layers\Domain\Workflow\Observer\Mediatheque\Response\OBCreateIndexBodyJson as OBMediathequeCreateIndexBodyJson;
use Sfynx\MediaBundle\Layers\Domain\Service\Token\TokenService;

use Sfynx\SpecificationDoctrine\Application\Handler\QueryHandler;

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
    /** @var TokenService */
    protected $tokenService;
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
     * @param TokenService $tokenService
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
        TokenService $tokenService,
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

        $this->tokenService = $tokenService;
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

        $options['criteria'] = \json_decode('{"field": "genre_produit_id", "operator": "=", "value": 3}', true);
        $options['criteria'] = \json_decode('{
                    "and":[
                        {"field": "genre_produit_id", "operator": "!=", "value": ""},
                        {"field": "genre_produit_id", "operator": "!=", "value": "0"}
                    ]
         }', true);
        $options['criteria'] = \json_decode('{
            "or":[
                {
                    "and":[
                        {"field": "lastName", "operator": "like", "value": "S%"},
                        {"field": "sex", "operator": "=", "value": "F"}
                    ]
                },
                {
                    "xor":[
                        {"field": "firstName", "operator": "like", "value": "%John%"},
                        {
                            "not":[
                                {"field": "sex", "operator": "=", "value": "M"}
                            ]
                        }
                    ]
                }
            ]
        }', true);


//        SELECT a, u, p, a
//        FROM Sfynx\MediaBundle\Layers\Domain\Entity\Mediatheque a
//        WHERE
//            (
//                (
//                    (
//                        ((a.civility.lastName LIKE ?0))
//                        AND
//                        ((a.civility.sex = ?1))
//                    )
//                )
//                OR
//                (
//                    (
//                        (a.civility.firstName LIKE ?2)
//                        AND
//                        ( NOT (  (NOT ((a.civility.sex = ?3) ))  ) )
//                    )
//                    OR
//                    (
//                        ( NOT ( (a.civility.firstName LIKE ?2) ) )
//                        AND
//                        (NOT ((a.civility.sex = ?3) ))
//                    )
//                )
//            )
//        ORDER BY a.group.description ASC

//        $start = \microtime(true);
//        $options['select'] = \json_decode('["u", "p"]', true);
//        $options['limit'] = \json_decode('{"start": 0, "count": 100}', true); # ex.: {"start": 0, "count": 100}
//        $options['orderBy'] = \json_decode('[{"field": "description", "asc": true}]', true); # ex.: [{"field": "xxx", "asc": true}]
//
//        $searchBy = new QueryHandler($options, $this->manager->getQueryRepository(), new \Sfynx\SpecificationDoctrine\Infrastructure\Persistence\FieldsDefinition\Group);
//        $result = $searchBy->process('orm');
//        $end = \microtime(true);
//        dump(sprintf('time execution in seconds: %s', round($end - $start, 5)));
//
//        dump($result);
////        dump($result->getArrayResults());
//        exit;

        // 1. Transform Request to Query.
        $adapter = new QueryAdapter(new IndexQuery());
        $query = $adapter->createQueryFromRequest(new IndexQueryRequest($this->request));

        $query->isServerSide = true ;

        // 2. Implement the query workflow
        $workflowQuery = (new QueryWorkflow())
            ->attach(new OBMediathequeIndexCreateQueryHandler($this->manager, $this->request))
            ->attach(new OBMediathequeIndexCreateJsonQueryHandler($this->manager, $this->request))
            ->attach(new OBMediathequeIndexFindEntitiesHandler(
                $this->manager,
                $this->request,
                $this->authorizationChecker,
                $this->roleFactory,
                $this->routeFactory
            ));

        // 3. Aapply the query workflow from the query
        $queryHandlerResult = (new IndexQueryHandler($workflowQuery))->process($query);
        if (!($queryHandlerResult instanceof HandlerInterface)) {
            throw PresentationException::invalidCommandHandlerResponse();
        }

        // 4. Implement the Response workflow
        $this->setParam('templating', 'SfynxMediaBundle:Mediatheque:index.html.twig');
        $workflowHandler = (new WorkflowHandler())
            ->attach(new OBCreateIndexBodyHtml($this->request, $this->templating, $this->param))
            ->attach(new OBCreateResponseHtml($this->request))
            ->attach(new OBMediathequeCreateIndexBodyJson(
                $this->tokenService,
                $this->request,
                $this->roleFactory,
                $this->toolExtension,
                $this->routeFactory,
                $this->translator,
                $this->param
            ))
            ->attach(new OBCreateIndexResponseJson($this->request));

        // 5. Implement the responseHandler from the workflow
        $this->responseHandler = new ResponseHandler($workflowHandler);
        $responseHandlerResult = $this->responseHandler->process($queryHandlerResult);

        return $responseHandlerResult->response;
    }
}