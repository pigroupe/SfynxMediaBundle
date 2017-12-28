<?php
namespace Sfynx\MediaBundle\Layers\Domain\Service\StorageProvider;

use Sfynx\MediaBundle\Layers\Domain\Entity\Media;
use Sfynx\MediaBundle\Layers\Domain\Service\StorageProvider\Generalisation\AbstractStorageProvider;

class MediaStorageProvider extends AbstractStorageProvider
{
    /**
     * {@inheritdoc}
     */
    public function doAdd(Media & $media)
    {
        if (!$this->em) {
            return false;
        }
        // Update case
        if ($media->getProviderReference()) {
            if (null === $media->getUploadedFile()) {
                if (!empty($media->getMetadata())) {
                    $this->update($media);

                    return true;
                }

                return false;
            } else {
                // Reupload case, remove the previous associated media
                $this->remove($media->getProviderReference());
            }
        }

        if (null !== $media->getUploadedFile()) {
            $this->create($media);

            $media->setProviderData($apiMedia);
            $media->setMimeType($apiMedia['mimeType']);
            $media->setProviderReference($apiMedia['reference']);
            $media->setExtension($apiMedia['extension']);
            $media->setPublicUri($apiMedia['publicUri']);

            $this->em->getConnection()->insert($this->getOwningTable($entity), $media);

            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function create(Media & $media, ?array $metadata)
    {
        $media
            ->setSource($this->getSourceName())
            ->setName($media->getUploadedFile()->getClientOriginalName())
            ->setMetadata($media->getMetadata())
            ->setMedia(curl_file_create(
                    $media->getUploadedFile()->getPathName(),
                    $media->getUploadedFile()->getMimeType(),
                    $media->getUploadedFile()->getClientOriginalName()
                )
            )
            ;

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function update(Media & $media)
    {
        $media
        ->setProviderReference('/media/%s')
        ->setMetadata($media->getMetadata())
        ;

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(Media & $media)
    {
        $this->em->getConnection()->delete(
            $this->getOwningTable($entity),
            [ $media->getProviderReference() => $reference]
        );

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getMediaPublicUrl($reference)
    {}
}
