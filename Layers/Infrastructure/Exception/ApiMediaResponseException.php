<?php
namespace Sfynx\MediaBundle\Layers\Infrastructure\Exception;

use Sfynx\MediaBundle\Layers\Infrastructure\Exception\Interfaces\ExceptionInterface;

class ApiMediaResponseException extends \Exception implements ExceptionInterface
{
    /**
     * {@inheritdoc}
     */
    public function __construct(array $options)
    {
        list($method, $body) = array_values($options);
        $message = sprintf('%s API METHOD::%s', $method, $body);

        parent::__construct($message);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getDetailedMessage()
    {
        return 'Specific information from media recording failure';
    }
}
