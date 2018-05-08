<?php
namespace Sfynx\MediaBundle\Layers\Application\Cqrs\Mediatheque\Command\Validation\ValidationHandler;

use Sfynx\CoreBundle\Layers\Application\Command\Validation\ValidationHandler\Generalisation\AbstractCommandValidationHandler;
use Sfynx\CoreBundle\Layers\Application\Command\Generalisation\Interfaces\CommandInterface;
use Sfynx\CoreBundle\Layers\Application\Validation\Validator\Constraint\AssocAll;
use Symfony\Component\Validator\Constraints as Assert;
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
            ->add('_token', new Assert\Optional(new Assert\NotBlank()))

            ->add('entityId', new Assert\Optional([
                new Assert\NotBlank(),
                new Assert\Regex('/^[0-9]+$/')
            ]))
            ->add('category', new Assert\Required(new Assert\NotBlank()))
            ->add('noLayout', new Assert\Optional(new Assert\Type('boolean')))
            ->add('status', new Assert\Optional(new Assert\NotBlank()))
            ->add('title', new Assert\Required(new Assert\NotBlank()))
            ->add('descriptif', new Assert\Optional(new Assert\NotBlank()))
            ->add('url', new Assert\Optional(new Assert\NotBlank()))
            ->add('mediadelete', new Assert\Optional(new Assert\Type('boolean')))
            ->add('copyright', new Assert\Optional(new Assert\NotBlank()))
            ->add('position', new Assert\Optional(new Assert\NotBlank()))
            ->add('publishedAt', new Assert\Optional([
                new Assert\NotBlank(),
//                new Assert\DateTime(['format' => 'Y-m-d'])
            ]))
            ->add('archived', new Assert\Optional(new Assert\Type('boolean')))
            ->add('enabled', new Assert\Required(new Assert\Type('boolean')))
            ->add('image', new Assert\Required(new Assert\Type('array')))
        ;
    }
}
