<?php
namespace Sfynx\MediaBundle\Layers\Presentation\Coordination\Mediatheque\Query;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Sfynx\CoreBundle\Layers\Presentation\Coordination\Generalisation\AbstractQueryController;
use Sfynx\CoreBundle\Layers\Presentation\Request\Query\ShowQueryRequest;
use Sfynx\CoreBundle\Layers\Presentation\Adapter\Query\QueryAdapter;
use Sfynx\CoreBundle\Layers\Application\Query\ShowQuery;
use Sfynx\CoreBundle\Layers\Application\Query\Workflow\QueryWorkflow;
use Sfynx\CoreBundle\Layers\Application\Query\Handler\ShowQueryHandler;
use Sfynx\CoreBundle\Layers\Application\Common\Handler\WorkflowHandler;
use Sfynx\CoreBundle\Layers\Application\Common\Generalisation\Interfaces\HandlerInterface;
use Sfynx\CoreBundle\Layers\Application\Response\Handler\Generalisation\Interfaces\ResponseHandlerInterface;
use Sfynx\CoreBundle\Layers\Application\Response\Handler\ResponseHandler;
use Sfynx\CoreBundle\Layers\Domain\Workflow\Observer\Query\OBEntityShowHandler;
use Sfynx\CoreBundle\Layers\Domain\Workflow\Observer\Response\OBCreateShowBody;
use Sfynx\CoreBundle\Layers\Domain\Workflow\Observer\Response\OBCreateResponseHtml;
use Sfynx\CoreBundle\Layers\Infrastructure\Exception\PresentationException;

/**
 * Show controller.
 *
 * @category   Sfynx\MediaBundle\Layers
 * @package    Presentation
 * @subpackage Coordination\Mediatheque\Query
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 */
class ShowController extends AbstractQueryController
{
    /** @var  ResponseHandlerInterface */
    protected $responseHandler;

    /**
     * Finds and displays a user entity.
     *
     * @param integer $id
     * @return Response
     * @access public
     * @throws AccessDeniedException
     */
    public function coordinate()
    {
        if (false === $this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        // 1. Transform Request to Query.
        $adapter = new QueryAdapter(new ShowQuery());
        $query = $adapter->createQueryFromRequest(
            new ShowQueryRequest($this->request)
        );

        // 2. Implement the query workflow
        $Observer1 = new OBEntityShowHandler($this->request, $this->manager);
        $workflowQuery = (new QueryWorkflow())
            ->attach($Observer1);

        // 3. Aapply the query workflow from the query
        $queryHandlerResult = (new ShowQueryHandler($workflowQuery))->process($query);
        if (!($queryHandlerResult instanceof HandlerInterface)) {
            throw PresentationException::invalidCommandHandlerResponse();
        }

        // 4. Implement the Response workflow
        $this->param->templating = str_replace('::', ':', $this->getParamOrThrow('sfynx_auth_theme_login')) . 'Users:show.html.twig';
        $Observer1 = new OBCreateShowBody($this->request, $this->templating, $this->param);
        $Observer2 = new OBCreateResponseHtml($this->request);
        $workflowHandler = (new WorkflowHandler())
            ->attach($Observer1)
            ->attach($Observer2);

        // 5. Implement the responseHandler from the workflow
        $this->responseHandler = new ResponseHandler($workflowHandler);
        $responseHandlerResult = $this->responseHandler->process($queryHandlerResult);

        return $responseHandlerResult->response;
    }
}