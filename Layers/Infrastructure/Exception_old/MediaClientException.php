<?php
namespace Sfynx\MediaBundle\Layers\Infrastructure\Exception;

class MediaClientException extends \Exception
{
    /**
     * The constructor.
     */
    public function __construct($operation)
    {
        parent::__construct(sprintf('%s Operation in Media is fail', $operation));
    }
}
