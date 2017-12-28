<?php
namespace Sfynx\MediaBundle\Layers\Domain\Service\StorageProvider\Generalisation\Interfaces;

use Doctrine\ORM\EntityManagerInterface;

use Sfynx\MediaBundle\Layers\Domain\Entity\Media;

interface StorageProviderInterface
{
    /**
     * Get SourceName
     *
     * @return MediaStorageProvider
     */
    public function setEm(EntityManagerInterface $em);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * Add a media
     *
     * @param  Media $media
     * @return boolean
     */
    public function add(Media & $media);

    /**
     * Do create a media
     *
     * @param  Media $media
     * @return string
     */
    public function create(Media & $media, ?array $metadata);

    /**
     * Do update a media
     *
     * @param  Media $media
     * @return boolean
     */
    public function update(Media & $media);

    /**
     * Do remove a media
     *
     * @param  string $reference
     * @return boolean
     */
    public function remove(Media & $media);

    /**
     * Do get MediaPublic url
     *
     * @param  string $reference
     * @return string
     */
    public function getMediaPublicUrl($reference);
}
