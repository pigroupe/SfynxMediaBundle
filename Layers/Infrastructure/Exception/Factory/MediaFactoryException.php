<?php
namespace Sfynx\MediaBundle\Layers\Infrastructure\Exception\Factory;

/**
 * Provides exception functions
 *
 * @SuppressWarnings("PHPMD.StaticAccess")
 */
class MediaFactoryException
{
    /** @var string */
    const exceptionsNamespace = 'Sfynx\MediaBundle\Layers\Infrastructure\Exception';

    /**
     * returns all curl error codes and their related exception classes
     *
     * @return array[$errorCode => $namespace]
     */
    const exceptionClass = [
            1   => 'MediaClientException',
            2   => 'ApiMediaResponseException',
    ];

    /**
     * throws a curl exception
     *
     * @param  string $message
     * @param  int    $code
     * @throws CurlExceptions\CurlException | \RuntimeException
     */
    public static function assertException(array $options, $code)
    {
        if (!array_key_exists($code, self::exceptionClass)) {
            throw new \InvalidArgumentException("Error: {$message} and the Error no is: {$code} ", $code);
        }

        $exceptionClass = self::exceptionsNamespace . '\\' . self::exceptionClass[$code];
        if (!class_exists($exceptionClass)) {
            throw new \RuntimeException(
                $exceptionClass . ' does not exist. Check class var $exceptionCodeMappings in ' . str_replace('\Factory', '', __NAMESPACE__)
            );
        }
        throw new $exceptionClass($options, $code);
    }
}