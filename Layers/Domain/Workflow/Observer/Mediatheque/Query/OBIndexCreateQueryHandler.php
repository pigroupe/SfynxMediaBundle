<?php
namespace Sfynx\MediaBundle\Layers\Domain\Workflow\Observer\Mediatheque\Query;

use stdClass;
use Exception;
use Sfynx\CoreBundle\Layers\Domain\Workflow\Observer\Generalisation\Query\AbstractIndexCreateQueryHandler;
use Sfynx\CoreBundle\Layers\Infrastructure\Exception\EntityException;

/**
 * Abstract Class OBIndexCreateQueryHandler
 *
 * @category Sfynx\CoreBundle\Layers
 * @package Domain
 * @subpackage Workflow\Observer\Query
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2016 PI-GROUPE
 */
class OBIndexCreateQueryHandler extends AbstractIndexCreateQueryHandler
{
    /**
     * This method implements the init process evenif the request and the form state
     * @return void
     * @throws EntityException
     */
    protected function process(): void
    {
        try {
//            print_r(($this->manager->getQueryRepository()->find('046e0589-637d-482f-a56f-fd552f55fb02'))); # 046e0589-637d-482f-a56f-fd552f55fb02
//            exit;

            $this->wfLastData->query = $this->manager->getQueryRepository()->formAllByCategory(
                $this->wfQuery->getCategory(),
                null,
                '',
                '',
                false
            );
        } catch (Exception $e) {
            throw EntityException::NotFoundEntity($this->entityName);
        }
    }
}
