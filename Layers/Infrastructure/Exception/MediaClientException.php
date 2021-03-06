<?php
namespace Sfynx\MediaBundle\Layers\Infrastructure\Exception;

use Sfynx\MediaBundle\Layers\Infrastructure\Exception\Interfaces\ExceptionInterface;

class MediaClientException extends \Exception implements ExceptionInterface
{
    /**
     * {@inheritdoc}
     */
    public function __construct(array $options)
    {
        list($body) = array_values($options);
        $message = sprintf('%s Operation in Media is fail', $body);
        parent::__construct($message);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getDetailedMessage()
    {
        return 'General information from media recording failure';
    }
}
