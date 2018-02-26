<?php
namespace Sfynx\MediaBundle\Layers\Domain\Workflow\Observer\Mediatheque\Query;

use stdClass;
use Exception;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

use Sfynx\AuthBundle\Infrastructure\Role\Generalisation\RoleFactoryInterface;
use Sfynx\ToolBundle\Builder\RouteTranslatorFactoryInterface;
use Sfynx\CoreBundle\Layers\Domain\Service\Manager\Generalisation\Interfaces\ManagerInterface;
use Sfynx\CoreBundle\Layers\Domain\Service\Request\Generalisation\RequestInterface;
use Sfynx\CoreBundle\Layers\Domain\Workflow\Observer\Generalisation\Query\AbstractIndexFindEntitiesHandler;
use Sfynx\CoreBundle\Layers\Infrastructure\Exception\EntityException;

/**
 * Abstract Class OBIndexFindEntitiesHandler
 *
 * @category Sfynx\MediaBundle\Layers
 * @package Domain
 * @subpackage Workflow\Observer\Mediatheque\Query
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2016 PI-GROUPE
 */
class OBIndexFindEntitiesHandler extends AbstractIndexFindEntitiesHandler
{
    /** @var AuthorizationCheckerInterface */
    protected $authorizationChecker;
    /** @var RoleFactoryInterface */
    protected $roleFactory;
    /** @var RouteTranslatorFactoryInterface */
    protected $routeFactory;

    /**
     * OBIndexFindEntitiesHandler constructor.
     * @param ManagerInterface $manager
     * @param RequestInterface $request
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param RoleFactoryInterface $roleFactory
     * @param RouteTranslatorFactoryInterface $routeFactory
     */
    public function __construct(
        ManagerInterface $manager,
        RequestInterface $request,
        AuthorizationCheckerInterface $authorizationChecker,
        RoleFactoryInterface $roleFactory,
        RouteTranslatorFactoryInterface $routeFactory
    ) {
        parent::__construct($manager, $request);

        $this->authorizationChecker = $authorizationChecker;
        $this->roleFactory = $roleFactory;
        $this->routeFactory = $routeFactory;
    }

    /**
     * This method implements the init process evenif the request and the form state
     * @return void
     * @throws EntityException
     */
    protected function process(): void
    {
        try {
            if (!$this->authorizationChecker->isGranted('ROLE_ADMIN')) {
                $route = $this->request->get('_route');
                if ((empty($route) || ($route == "_internal"))) {
                    $locale = $this->request->getLocale();
                    $route  = $this->routeFactory->getMatchParamOfRoute('_route', $locale);
                }
                $user_roles = $this->roleFactory->getAllUserRoles();

                $this->wfLastData->query = $this->manager->getQueryRepository()
                    ->checkRoles($this->wfLastData->query, $user_roles, $route);
            }

            $this->wfLastData->entities = $this->manager->getQueryRepository()->findTranslationsByQuery(
                $this->wfQuery->getLocale(),
                $this->wfLastData->query->getQuery(),
                'object',
                false
            );
        } catch (Exception $e) {
            throw EntityException::NotFoundEntities($this->entityName);
        }
    }
}
