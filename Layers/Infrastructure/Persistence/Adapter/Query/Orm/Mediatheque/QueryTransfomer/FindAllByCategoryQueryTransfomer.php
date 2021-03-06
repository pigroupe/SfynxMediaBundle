<?php
/*
 * This file is generated by SFYNX CORE GENERATOR.
 *
 * (c) Etienne de Longeaux <sfynx@pi-groupe.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sfynx\MediaBundle\Layers\Infrastructure\Persistence\Adapter\Query\Orm\Mediatheque\QueryTransfomer;

use Sfynx\CoreBundle\Layers\Infrastructure\Persistence\QueryBuilder\Generalisation\Orm\AbstractQueryBuilder;
use Sfynx\MediaBundle\Layers\Infrastructure\Persistence\Adapter\Query\Orm\Mediatheque\QueryBuilder\FindAllByCategoryQueryBuilder;

/**
 * Class FindAllByCategoryQueryTransfomer
 *
 * @category MyContext
 * @package Infrastructure
 * @subpackage Persistence\Adapter\Query\Orm\Movie\QueryTransfomer
 *
 * @author SFYNX <sfynx@pi-groupe.net>
 * @link http://www.sfynx.fr
 * @license LGPL (https://opensource.org/licenses/LGPL-3.0)
 */
class FindAllByCategoryQueryTransfomer extends AbstractQueryBuilder
{
    /**
     * @param bool $var1
     * @param bool $var2
     * @param int $var3
     * @param array $var4
     * @return array
     */
    public function __invoke(
        $category = '',
        $MaxResults = null,
        $ORDER_PublishDate = '',
        $ORDER_Position = '',
        $enabled = true,
        $with_archive = false
    ) {
        $query = (new FindAllByCategoryQueryBuilder($this->entityRepository))
            ->__invoke($category, $MaxResults, $ORDER_PublishDate, $ORDER_Position, $enabled, $with_archive);

        return $query;
    }
}
