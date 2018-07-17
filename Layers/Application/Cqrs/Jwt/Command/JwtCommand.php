<?php
namespace Sfynx\MediaBundle\Layers\Application\Cqrs\Jwt\Command;

/**
 * Class JwtToken
 *
 * @category   Sfynx\MediaBundle\Layers
 * @package    Application
 * @subpackage Cqrs\Jwt\Command
 */
class JwtCommand
{
    /** @var string Unique signing-key identifier. */
    protected $kid;

    /** @var string Encoded token string containing all information. */
    protected $token;

    /**
     * JwtToken constructor.
     * @param string $kid Unique signing-key identifier.
     * @param string $token Encoded token string containing all information.
     */
    public function __construct(string $kid, string $token)
    {
        $this->kid = $kid;
        $this->token = $token;
    }

    /**
     * Get the unique signing-key identifier.
     * @return string
     */
    public function getKid(): string
    {
        return $this->kid;
    }

    /**
     * Get the token string.
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }
}
