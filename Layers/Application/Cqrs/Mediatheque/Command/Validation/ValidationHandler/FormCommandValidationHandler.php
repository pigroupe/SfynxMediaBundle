<?php
namespace Sfynx\MediaBundle\Layers\Application\Cqrs\Mediatheque\Command\Validation\ValidationHandler;

use Sfynx\CoreBundle\Layers\Application\Validation\Generalisation\ValidationHandler\AbstractCommandValidationHandler;
use Sfynx\CoreBundle\Layers\Application\Command\Generalisation\Interfaces\CommandInterface;
use Sfynx\CoreBundle\Layers\Application\Validation\Validator\Constraint\AssocAll;
use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Optional;
use Symfony\Component\Validator\Constraints\Required;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints\Callback;

/**
 * Class FormCommandValidationHandler.
 *
 * @category   Sfynx\MediaBundle\Layers
 * @package    Application
 * @subpackage Cqrs\Mediatheque\Command\Validation\ValidationHandler
 */
class FormCommandValidationHandler extends AbstractCommandValidationHandler
{
    /** @var bool */
    protected $skipArrayValidator = [];

    protected function initConstraints(CommandInterface $command): void
    {
        $this
            ->add('_token', new Optional(new NotBlank()))

            ->add('entityId', new Optional(new NotBlank()))
            ->add('category', new Required(new NotBlank()))
            ->add('NoLayout', new Optional(new Type('boolean')))
            ->add('status', new Optional(new NotBlank()))
            ->add('title', new Required(new NotBlank()))
            ->add('descriptif', new Optional(new NotBlank()))
            ->add('url', new Optional(new NotBlank()))
            ->add('mediadelete', new Optional(new Type('boolean')))
            ->add('copyright', new Optional(new NotBlank()))
            ->add('position', new Optional(new NotBlank()))
            ->add('publishedAt', new Optional(new NotBlank()))
            ->add('archived', new Optional(new Type('boolean')))
            ->add('enabled', new Required(new Type('boolean')))
            ->add('image', new Required(new Type('array')))
        ;
    }
}
