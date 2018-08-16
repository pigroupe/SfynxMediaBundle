<?php
namespace Sfynx\MediaBundle\Layers\Domain\Service\StorageProvider;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\EntityManagerInterface;

use Sfynx\RestClientBundle\Http\Rest\Generalisation\Interfaces\RestApiClientInterface;
use Sfynx\RestClientBundle\Http\Rest\RestApiClientBasicImplementor;
use Sfynx\RestClientBundle\Http\Asynchronous;
use Sfynx\RestClientBundle\Http\Response;
use Sfynx\RestClientBundle\Exception\ApiHttpResponseException;
use Sfynx\MediaBundle\Layers\Domain\Entity\Media;
use Sfynx\MediaBundle\Layers\Domain\Service\StorageProvider\Generalisation\AbstractStorageProvider;
use Sfynx\MediaBundle\Layers\Infrastructure\Exception\Factory\MediaFactoryException;

/**
 * Api provider class to upload files
 *
 * @category   Sfynx\MediaBundle\Layers
 * @package    Domain
 * @subpackage Service\StorageProvider
 * @author   Etienne de Longeaux <etienne.delongeaux@gmail.com>
 */
class ApiMediaStorageProvider extends AbstractStorageProvider
{
    /** @var RestApiClientInterface */
    protected $restClient;
    /** @var int */
    protected $quality;

    /**
     * Constructor
     *
     * @param null|RestApiClientInterface $restClient
     * @param int $quality
     */
    public function __construct(RestApiClientInterface $restClient = null, int $quality = 95)
    {
        $this->restClient = $restClient;
        $this->quality = $quality;
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
    protected function doAdd(Media &$media)
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
                if (!empty($metadata)) {
                    $response = $this->update($media, $metadata);
                    if ($this->em instanceof EntityManagerInterface) {
                        $this->em->getConnection()->update($this->getOwningTable($entity), $media->__toArray());
                    }

                    return true;
                }

                return false;
            } else {
                // Reupload case, remove the previous associated media
                try {
                    $response = $this->remove($media);
                } catch (ApiHttpResponseException $e) {
                }
            }
        }
        
        if (null !== $media->getUploadedFile()) {
            $response = $this->create($media, $metadata);
            $apiMedia = json_decode($response->getContent(), true);

            $media->setProviderData($apiMedia);
            $media->setMimeType($apiMedia['mimeType']);
            $media->setProviderReference($apiMedia['reference']);
            $media->setExtension($apiMedia['extension']);
            $media->setPublicUri($apiMedia['publicUri']);

            // set default quality value if not register
            if (null === $media->getQuality()) {
                $media->setQuality($this->quality);
            }
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
    public function create(Media & $media, ?array $metadata): Response
    {
        return $this
        ->getRestClient()
        ->post('/media', [
            'source' => $media->getSourceName(),
            'storage_provider' => $media->getProviderName(),
            'name' => $media->getUploadedFile()->getClientOriginalName(),
            'description' => $media->getDescriptif(),
            'quality' => $media->getQuality(),
            'metadata' => \array_merge($metadata, [
                'idMedia' => $media->getId(),
                'title' => $media->getTitle(),
                'description' => $media->getDescriptif(),
            ]),
            'signing' => [
                'connected' => $media->getConnected(),
                'roles' => $media->getRoles(),
                'usernames' => $media->getUsernames(),
                'rangeip' => $media->getRangeIp(),
            ],
            'media' => \curl_file_create(
                $media->getUploadedFile()->getPathName(),
                $media->getUploadedFile()->getClientMimeType(),
                $media->getUploadedFile()->getClientOriginalName()
            ),
            'enabled' => $media->getEnabled(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function createFromFormats(Media & $media, ?array $formats, ?array $formatsCreation): void
    {
        if ($media->isImageable()
            && isset($formatsCreation['parallel_limit'])
            && isset($formatsCreation['curlopt_timeout_ms'])
            && isset($formatsCreation['timeout_wait_response'])
        ) {
            $urls = [];
            foreach ($formats as $queries) {
                $queries = \array_filter($queries);
                if (!empty($queries)) {
                    $queries['noresponse'] = 1;
                    \array_push($urls, RestApiClientBasicImplementor::addQueryString($media->getUrl(), $queries));
                }
            }

            /*
             * API in 3 s execution, with 0.05 Time, in seconds, to wait for a response.
             * 10 requests  => CURLOPT_TIMEOUT_MS = 150
             * 20 requests  => CURLOPT_TIMEOUT_MS = 250
             * 100 requests => CURLOPT_TIMEOUT_MS = 1000
             * n requests    => CURLOPT_TIMEOUT_MS = n * 15
             */
            $asyncRequest = new Asynchronous\AsyncRequest();
            $asyncRequest->setParallelLimit($formatsCreation['parallel_limit']);
            foreach ($urls as $url) {
                $request = new Asynchronous\Request($url, $formatsCreation['curlopt_timeout_ms']);
                $asyncRequest->enqueue($request);
            }
            $asyncRequest->run($formatsCreation['timeout_wait_response']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function update(Media & $media, ?array $metadata): Response
    {
        return $this
            ->getRestClient()
            ->put(sprintf('/media/%s', $media->getProviderReference()), [
                'metadata' => array_merge($metadata, [
                    'title' => $media->getTitle(),
                    'description' => $media->getDescriptif()
            ])
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function remove(Media &$media): Response
    {
        $reference = $media->getProviderReference();

        return $this
            ->getRestClient()
            ->delete(sprintf('/media/%s', $reference));
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
