<?php
namespace Sfynx\MediaBundle\Layers\Domain\Service\StorageProvider\Generalisation\Interfaces;

use Doctrine\ORM\EntityManagerInterface;

use Sfynx\RestClientBundle\Http\Response;
use Sfynx\MediaBundle\Layers\Domain\Entity\Media;

interface StorageProviderInterface
{
    /**
     * Get SourceName
     *
     * @return StorageProviderInterface
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
     * @param array $metadata
     * @return Response
     */
    public function create(Media & $media, ?array $metadata): Response;

    /**
     * @param  Media $media
     * @param array|null $formats
     * @param array|null $formatsCreation
     * @return void
     */
    public function createFromFormats(Media & $media, ?array $formats, ?array $formatsCreation): void;

    /**
     * Do update a media
     *
     * @param  Media $media
     * @param array $metadata
     * @return Response
     */
    public function update(Media & $media, ?array $metadata): Response;

    /**
     * Do remove a media
     *
     * @param  string $reference
     * @return Response
     */
    public function remove(Media & $media): Response;

    /**
     * Do get MediaPublic url
     *
     * @param  string $reference
     * @return string
     */
    public function getMediaPublicUrl($reference);
}
