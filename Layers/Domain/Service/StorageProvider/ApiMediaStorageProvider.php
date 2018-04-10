<?php
namespace Sfynx\MediaBundle\Layers\Domain\Service\StorageProvider;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\EntityManagerInterface;

use Sfynx\RestClientBundle\Exception\ApiHttpResponseException;
use Sfynx\RestClientBundle\Http\Rest\RestApiClientInterface;
use Sfynx\MediaBundle\Layers\Domain\Entity\Media;
use Sfynx\MediaBundle\Layers\Domain\Service\StorageProvider\Generalisation\AbstractStorageProvider;

/**
 * Api provider class to upload files
 *
 * @category   Sfynx\MediaBundle\Layers
 * @package    Domain
 * @subpackage EventSubscriber
 * @author   Etienne de Longeaux <etienne.delongeaux@gmail.com>
 */
class ApiMediaStorageProvider extends AbstractStorageProvider
{
    /** @var RestApiClientInterface */
    protected $restClient;

    /**
     * Constructor
     *
     * @param RestApiClientInterface $restClient
     */
    public function __construct($restClient = null)
    {
        $this->restClient = $restClient;
    }

    /**
     * Get MediaClient
     *
     * @return RestApiClientInterface
     */
    public function getRestClient()
    {
        return $this->restClient;
    }

    /**
     * {@inheritdoc}
     */
    protected function doAdd(Media & $media)
    {
        $metadata = $media->getMetadata();
        if (!is_array($media->getMetadata())
            && null !== $metadata
        ) {
            $metadata = json_decode($metadata, true);
        }

        // 1. Upload case case
        if (null == $media->getUploadedFile()
            && isset($metadata['form_name'])
            && isset($metadata['field_form'])
        ) {
                $form_name = $metadata['form_name'];
                $field_form = $metadata['field_form'];

                if (null != ($_FILES[$form_name]['tmp_name'][$field_form]['uploadedFile'])) {
                    $path = $_FILES[$form_name]['tmp_name'][$field_form]['uploadedFile'];
                    $originalName = $_FILES[$form_name]['name'][$field_form]['uploadedFile'];
                    $mimeType = $_FILES[$form_name]['type'][$field_form]['uploadedFile'];
                    $size = $_FILES[$form_name]['size'][$field_form]['uploadedFile'];
                    $error = $_FILES[$form_name]['error'][$field_form]['uploadedFile'];

                    $UploadedFile = new UploadedFile($path, $originalName, $mimeType, $size, $error);
                    $media->setUploadedFile($UploadedFile);
                }
        }

        // 2. InsertUpdate case
        if ($media->getProviderReference()) {
            if (null === $media->getUploadedFile()) {
                if (!empty($media->getMetadata())) {
                    $response = $this->update($media);
                    if (!$response) {
                        return false;
                    }

                    if ($this->em instanceof EntityManagerInterface) {
                        $this->em->getConnection()->update($this->getOwningTable($entity), $media->__toArray());
                    }

                    return true;
                }
                return false;
            } else {
                // Reupload case, remove the previous associated media
                $response = $this->remove($media);
                if (!$response) {
                    return false;
                }

//                if ($this->em instanceof EntityManagerInterface) {
//                    $this->em->getConnection()->delete($this->getOwningTable($entity), [$media->getId()]);
//                }
            }
        }

        if (null !== $media->getUploadedFile()) {
            $response = $this->create($media, $metadata);
            if (!$response) {
                return false;
            }
            $apiMedia = json_decode($response->getContent(), true);

            $media->setProviderData($apiMedia);
            $media->setMimeType($apiMedia['mimeType']);
            $media->setProviderReference($apiMedia['reference']);
            $media->setExtension($apiMedia['extension']);
            $media->setPublicUri($apiMedia['publicUri']);

            if ($this->em instanceof EntityManagerInterface) {
                $this->em->getConnection()->insert($this->getOwningTable($entity), $media->__toArray());
            }

            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function create(Media & $media, ?array $metadata)
    {
        try {
            return $this
                ->getRestClient()
                ->post('/media', [
                    'source' => $media->getSourceName(),
                    'storage_provider' => $media->getProviderName(),
                    'name' => $media->getUploadedFile()->getClientOriginalName(),
                    'description' => $media->getDescriptif(),
                    'metadata' => array_merge($metadata, [
                        'title' => $media->getTitle(),
                        'description' => $media->getDescriptif()
                    ]),
                    'media' => curl_file_create(
                        $media->getUploadedFile()->getPathName(),
                        $media->getUploadedFile()->getClientMimeType(),
                        $media->getUploadedFile()->getClientOriginalName()
                    )
                ])
            ;
        } catch (ApiHttpResponseException $e) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function update(Media & $media)
    {
        try {
            $this
                ->getRestClient()
                ->put(
                    sprintf('/media/%s', $media->getProviderReference()),
                    array('metadata' => $media->getMetadata())
                )
            ;
        } catch (ApiHttpResponseException $e) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(Media & $media)
    {
        $reference = $media->getProviderReference();
        try {
            $this
                ->getRestClient()
                ->delete('/media/'.$reference)
            ;
        } catch (ApiHttpResponseException $e) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getMediaPublicUrl($reference)
    {
        try {
            $raw = $this->getRestClient()->get('/endpoint')->getContent();
            $data = json_decode($raw, true);

            return sprintf('%s/media/%s',
                $data['publicEndpoint'],
                $reference
            );
        } catch (ApiHttpResponseException $e) {
            return false;
        }
    }
}
