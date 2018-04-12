<?php
namespace Sfynx\MediaBundle\Layers\Infrastructure\Exception\Interfaces;

/**
 * Contract for all exceptions that should provide detailed information
 *
 */
interface ExceptionInterface
{
    /**
     * Sets all necessary dependencies
     */
    public function __construct(array $options);

    /**
     * Returns a human readable and improved error message that explains the exception
     * and why it probably occures.
     *
     * @return string
     */
    public function getDetailedMessage();
}