<?php
namespace Sfynx\MediaBundle\Layers\Domain\Workflow\Observer\Mediatheque\Query;

use stdClass;
use Exception;
use Sfynx\CoreBundle\Layers\Domain\Workflow\Observer\Generalisation\Query\AbstractIndexCreateJsonQueryHandler;
use Sfynx\CoreBundle\Layers\Infrastructure\Exception\EntityException;

/**
 * Abstract Class OBIndexCreateJsonQueryHandler
 *
 * @category Sfynx\CoreBundle\Layers
 * @package Domain
 * @subpackage Workflow\Observer\Query
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2016 PI-GROUPE
 */
class OBIndexCreateJsonQueryHandler extends AbstractIndexCreateJsonQueryHandler
{
    /**
     * This method implements the init process evenif the request and the form state
     * @return void
     * @throws EntityException
     */
    protected function process(): void
    {
        try {
            $aColumns = [
                'a.id',
                'c.name',
                'a.status',
                'a.title',
                'a.enabled',
                'a.created_at',
                'a.updated_at',
                'a.enabled',
                'a.enabled'
            ];

            $q1 = clone $this->wfLastData->query;
            $q2 = clone $this->wfLastData->query;

            $this->wfLastData->result = $this->createAjaxQuery('select', $aColumns, $q1, 'a', [
                    0 => ['column'=>'a.created_at', 'format'=>'Y-m-d', 'idMin'=>'minc', 'idMax'=>'maxc'],
                    1 => ['column'=>'a.updated_at', 'format'=>'Y-m-d', 'idMin'=>'minu', 'idMax'=>'maxu']
                ]
//                , ['time' => 30, 'namespace' => 'hash_list_gedmomedia', 'mode' => \Doctrine\ORM\Cache::MODE_NORMAL]
            );
            $this->wfLastData->total  = $this->createAjaxQuery('count', $aColumns, $q2, 'a', [
                    0 => ['column'=>'a.created_at', 'format'=>'Y-m-d', 'idMin'=>'minc', 'idMax'=>'maxc'],
                    1 => ['column'=>'a.updated_at', 'format'=>'Y-m-d', 'idMin'=>'minu', 'idMax'=>'maxu']
                ]
//                , ['time' => 30, 'namespace' => 'hash_list_gedmomedia', 'mode' => \Doctrine\ORM\Cache::MODE_NORMAL]
            );
        } catch (Exception $e) {
            throw EntityException::NotFoundEntities($this->entityName);
        }
    }
}
