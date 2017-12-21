<?php
namespace Sfynx\MediaBundle\Layers\Infrastructure\Persistence\Adapter\Query\Orm;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\ORM\EntityManagerInterface;

use Sfynx\CoreBundle\Layers\Infrastructure\Persistence\Adapter\Generalisation\Orm\AbstractQueryRepository;

class MediathequeRepository extends AbstractQueryRepository
{
    /**
     * {@inheritdoc}
     */
    public function formAllByCategory($category = '', $MaxResults = null, $ORDER_PublishDate = '', $ORDER_Position = '', $enabled = true, $with_archive = false)
    {
        $query = $this->createQueryBuilder('a')
            ->select('a')
            ->leftJoin('a.image', 'm')
            ->leftJoin('a.category', 'c')
            ->andWhere('a.image IS NOT NULL')
            ;
        if (!empty($ORDER_PublishDate) && !empty($ORDER_Position)) {
            $query
                ->orderBy('a.published_at', $ORDER_PublishDate)
                ->addOrderBy('a.position', $ORDER_Position);
        } elseif (!empty($ORDER_PublishDate) && empty($ORDER_Position)) {
            $query
                ->orderBy('a.published_at', $ORDER_PublishDate);
        } elseif (empty($ORDER_PublishDate) && !empty($ORDER_Position)) {
            $query
                ->orderBy('a.position', $ORDER_Position);
        }
        if (!$with_archive) {
            $query->andWhere('a.archived = 0');
        }
        if ($enabled && !empty($category)) {
            $query
                ->andWhere('a.enabled = :enabled')
                ->andWhere('c.id = :cat')
                ->setParameters(array(
                    'cat'        => $category,
                    'enabled'    => 1,
                ))
            ;
        } elseif ($enabled && empty($category)) {
            $query
                ->andWhere('a.enabled = :enabled')
                ->setParameters(array(
                    'enabled'    => 1,
                ))
            ;
        } elseif (!$enabled && !empty($category)) {
            $query
                ->andWhere('c.id = :cat')
                ->setParameters(array(
                    'cat' => $category
                ))
            ;
        }
        if (!(null === $MaxResults)) {
            $query->setMaxResults($MaxResults);
        }

        return $query;
    }
}