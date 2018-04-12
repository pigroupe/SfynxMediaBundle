<?php
namespace Sfynx\MediaBundle\Layers\Domain\Service\StorageProvider\Generalisation;

use Doctrine\ORM\EntityManagerInterface;

use Sfynx\MediaBundle\Layers\Domain\Entity\Media;
use Sfynx\MediaBundle\Layers\Domain\Service\StorageProvider\Generalisation\Interfaces\StorageProviderInterface;

/**
 * Class AbstractStorageProvider
 * @abstract
 * @package Sfynx\MediaBundle\Layers\Domain\Service\StorageProvider\Generalisation
 */
abstract class AbstractStorageProvider implements StorageProviderInterface
{
    /** @var EntityManagerInterface */
    protected $em = null;

    /** @var string */
    protected $name;

    /** {@inheritdoc} */
    public function setEm(EntityManagerInterface $em)
    {
        $this->em = $em;
        return $this;
    }

    /** {@inheritdoc} */
    public function getName()
    {
        return $this->name;
    }

    /** {@inheritdoc} */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /** {@inheritdoc} */
    public function add(Media & $media)
    {
        return $this->doAdd($media);
    }

    /**
     * Do add a media
     *
     * @param  Media $media
     * @return boolean
     */
    abstract protected function doAdd(Media & $media);

    /** {@inheritdoc} */
    abstract public function create(Media & $media, ?array $metadata);

    /** {@inheritdoc} */
    abstract public function update(Media & $media, ?array $metadata);

    /** {@inheritdoc} */
    abstract public function remove(Media & $media);

    /** {@inheritdoc} */
    abstract public function getMediaPublicUrl($reference);

    /**
     * Gets the name of the table.
     *
     * @return string the name of the table entity that we have to insert.
     * @access private
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    protected function getOwningTable($entity = null)
    {
        if ($this->em && is_object($entity)) {
            $this->_class = $this->em->getClassMetadata(get_class($entity));
        }
        return $this->_class->table['name'];
    }
}
