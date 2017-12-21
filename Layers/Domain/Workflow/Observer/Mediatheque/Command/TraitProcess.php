<?php
namespace Sfynx\MediaBundle\Layers\Domain\Workflow\Observer\Mediatheque\Command;

use Exception;
use Symfony\Component\Form\Extension\Core\DataTransformer\ValueToDuplicatesTransformer;
use Sfynx\CoreBundle\Layers\Domain\Model\Interfaces\EntityInterface;

/**
 * Class TraitProcess
 *
 * @category Sfynx\MediaBundle\Layers
 * @package Domain
 * @subpackage Workflow\Observer\Mediatheque\Command
 * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright 2016 PI-GROUPE
 */
trait TraitProcess
{
    /**
     * {@inheritdoc}
     */
    protected function onSuccess(): void
    {
        $entity = $this->wfLastData->entity;
        try {
            if ($entity instanceof EntityInterface
                && count($this->wfCommand->errors) == 0
            ) {
                $entity = $this->manager->buildFromCommand($entity, $this->wfCommand);
                $this->manager->getCommandRepository()->save($entity);
                $this->manager->getCommandRepository()->getCacheFactory()->deleteAllCacheQuery('hash_list_gedmomedia');
            }
        } catch (Exception $e) {
            $this->wfCommand->errors['success'] = 'errors.user.save';
        }
        // we add the last entity version
        $this->wfLastData->entity = $entity;
    }
}
