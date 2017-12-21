<?php
namespace Sfynx\MediaBundle\Layers\Application\Cqrs\Mediatheque\Command\Validation\SpecHandler;

use Sfynx\SpecificationBundle\Specification\Generalisation\InterfaceSpecification;
use Sfynx\SpecificationBundle\Specification\Logical\XorSpec;
use Sfynx\SpecificationBundle\Specification\Logical\TrueSpec;
use Sfynx\CoreBundle\Layers\Application\Validation\Generalisation\SpecHandler\AbstractCommandSpecHandler;
use Sfynx\MediaBundle\Layers\Domain\Specification\Authorisation\SpecIsRoleAdmin;
use Sfynx\MediaBundle\Layers\Domain\Specification\Authorisation\SpecIsRoleUser;
use Sfynx\MediaBundle\Layers\Domain\Specification\Authorisation\SpecIsRoleAnonymous;

/**
 * Class UpdateCommandValidationHandler.
 *
 * @category   Sfynx\MediaBundle\Layers
 * @package    Application
 * @subpackage Cqrs\Mediatheque\Command\Validation\SpecHandler
 */
class FormCommandSpecHandler extends AbstractCommandSpecHandler
{
    /**
     * @return XorSpec
     */
    public function initSpecifications(): InterfaceSpecification
    {
        return new TrueSpec();
    }
}
