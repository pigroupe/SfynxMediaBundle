<?php
namespace Sfynx\MediaBundle\Layers\Domain\Service\Mediatheque\Manager;

use Sfynx\CoreBundle\Layers\Application\Command\Generalisation\Interfaces\CommandInterface;
use Sfynx\CoreBundle\Layers\Domain\Service\Manager\Generalisation\Interfaces\ManagerInterface;
use Sfynx\CoreBundle\Layers\Domain\Service\Manager\Generalisation\AbstractManager;
use Sfynx\CoreBundle\Layers\Domain\Repository\Command\CommandRepositoryInterface;
use Sfynx\CoreBundle\Layers\Domain\Repository\Query\QueryRepositoryInterface;

use Sfynx\MediaBundle\Layers\Domain\Entity\Media;
use Sfynx\MediaBundle\Layers\Domain\Repository\Command\MediathequeCommandRepositoryInterface;
use Sfynx\MediaBundle\Layers\Domain\Repository\Query\MediathequeQueryRepositoryInterface;

/**
 * Layout manager working with entities (Orm, Odm, Couchdb)
 *
 * @category   Sfynx\MediaBundle\Layers
 * @package    Domain
 * @subpackage Service\Mediatheque\Manager
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 */
class EntityManager extends AbstractManager implements ManagerInterface
{
    /**
     * @param string|null $className
     * @param array $args
     * @return MediathequeQueryRepositoryInterface
     */
    public function getQueryRepository(string $className = null, array $args = []): QueryRepositoryInterface
    {
        return parent::getQueryRepository($className, $args);
    }

    /**
     * @param string|null $className
     * @param array $args
     * @return MediathequeCommandRepositoryInterface
     */
    public function getCommandRepository(string $className = null, array $args = []): CommandRepositoryInterface
    {
        return parent::getCommandRepository($className, $args);
    }

    /**
     * {@inheritDoc}
     */
    public function newFromCommand(CommandInterface $command): object
    {
        $class = $this->getClass();
        $entity = $class::newFromCommand($command, ['image', 'image2']);
        $this->transformEntity($entity, $command);

        return $entity;
    }

    /**
     * {@inheritDoc}
     */
    public function buildFromCommand(object $entity, CommandInterface $command, bool $updateCommand = false): object
    {
        $class = $this->getClass();
        $entity = $class::buildFromCommand($entity, $command, ['image', 'image2'], $updateCommand);
        $this->transformEntity($entity, $command);

        return $entity;
    }

    /**
     * {@inheritDoc}
     */
    public function buildFromEntity(CommandInterface $command, object $entity): CommandInterface
    {
        $class = $this->getClass();
        $command = $class::buildFromEntity($command, $entity);

        return $command;
    }

    /**
     * @param object $entity
     * @param CommandInterface $command
     * @return EntityManager
     */
    protected function transformEntity(object &$entity, CommandInterface $command): EntityManager
    {
        if ('' !== $command->category && null !== $command->category) {
            $entity->setCategory(
                $this->getQueryRepository()->getEntityManager()->getReference(
                    '\PiApp\GedmoBundle\Layers\Domain\Entity\Category',
                    $command->category
                )
            );
        }

        if ((null !== $command->image)
            && is_null($entity->getImage())
        ) {
            $media = Media::newFromArray($command->image);
        } elseif (null !== $command->image) {
            $media = Media::buildFromArray($entity->getImage(), $command->image);
        } else {
            $media = Media::createFromNative();
        }
        $media->setUpdatedAt(new \DateTime());
        $entity->setImage($media);

        return $this;
    }
}
