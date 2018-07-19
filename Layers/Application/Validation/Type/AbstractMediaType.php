<?php
namespace Sfynx\MediaBundle\Layers\Application\Validation\Type;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use Sfynx\MediaBundle\Layers\Domain\Entity\Media;
use Sfynx\MediaBundle\Layers\Domain\EventSubscriber\StorageProviderHandler;
use Sfynx\MediaBundle\Layers\Infrastructure\Exception\MediaClientException;

/**
 * class AbstractMediaType
 *
 * @category   Sfynx\MediaBundle\Layers
 * @package    Application
 * @subpackage Validation\Type
 * @author   Etienne de Longeaux <etienne.delongeaux@gmail.com>
 */
class AbstractMediaType extends AbstractType
{
    /** @var StorageProviderHandler */
    protected $storageProviderHandler;

    /** @var ValidatorInterface */
    protected $validator;

    /**
     * {@inheritdoc}
     */
    public function __construct(StorageProviderHandler $storageProviderHandler, ValidatorInterface $validator = null)
    {
        $this->storageProviderHandler = $storageProviderHandler;
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('publicUri', Type\HiddenType::class, [
                'required' => false
            ])
            ->add('mimeType', Type\HiddenType::class, [
                'required' => false
            ])
            ->add('extension', Type\HiddenType::class, [
                'required' => false
            ])
            ->add('providerReference', Type\HiddenType::class, [
                'required' => false
            ])
            ->add('providerName', Type\HiddenType::class, [
                'data'  => $options['storage_provider'],
            ])
            ->add('sourceName', Type\HiddenType::class, [
                'data'  => $options['storage_source'],
            ])
            ->add('updated_at', null, [
                'widget' => 'single_text', // choice, text, single_text
                'attr' => ['style'=>'display:none;'],
                "label_attr" => [
                    "style"=> 'display:none;',
                ],
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired([
                'storage_provider',
                'storage_source',
                'metadata'
            ])
            ->setDefaults([
                'data_class' => Media::class,
                'storage_provider' => '',
                'storage_source' => '',
                'handler' => '',
                'context' => '',
                'metadata' => [
                    'form_name' => 'sfynx_mediabundle_mediatype_file',
                    'field_form' => 'media'
                ],
                'attr' => [
                    'class' => sprintf('sfynx_media_%s', $this->getName())
                ],
            ])
            ->setAllowedTypes('storage_provider', ['string'])
            ->setAllowedTypes('storage_source', ['string'])
            ->setAllowedTypes('handler', ['string'])
            ->setAllowedTypes('context', ['string'])
            ->setAllowedTypes('metadata', ['array'])
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    protected function setPreSetData(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function(FormEvent $event) use ($options) {
                $isUploadedFileRequired = $options['required'];
                $form = $event->getForm();
                $parent = $form->getParent()->getData();

                if (method_exists($parent, 'getEnabled')) {
                    $form->add('enabled', Type\HiddenType::class, [
                        'required' => false,
                        'data'  => $parent->getEnabled(),
                    ]);
                }
                if (method_exists($parent, 'getTitle')) {
                    $form->add('title', Type\HiddenType::class, [
                        'required' => false,
                        'data'  => $parent->getTitle(),
                    ]);
                }
                if (method_exists($parent, 'getDescriptif')) {
                    $form->add('descriptif', Type\HiddenType::class, [
                        'required' => false,
                        'data'  => $parent->getDescriptif(),
                    ]);
                }
                if (null !== $event->getData()) {
                    $isUploadedFileRequired = false;
                }

                $form->add('uploadedFile', Type\FileType::class, [
                    'label' => ' ',
                    'mapped' => false,
                    'required' => $isUploadedFileRequired,
                    'constraints' => $options['constraints']
                ]);

                $form->add('quality', Type\TextType::class, [
                    'data'  => $parent->getImage()->getQuality(),
                    'label' => 'pi.form.label.field.quality',
                    'label_attr' => [
                        'class'=> 'other_collection',
                    ],
                ]);

                $form->add('connected', Type\CheckboxType::class, [
                    'data'  => $parent->getImage()->getConnected(),
                    'label' => 'pi.form.label.field.connexion_oblige',
                    'label_attr' => [
                        'class'=> 'permission_collection',
                    ],
                ]);
                $form->add('roles', \Sfynx\AuthBundle\Application\Validation\Type\SecurityRolesType::class, [
                    'data'  => $parent->getImage()->getRoles(),
                    'multiple' => true,
                    'required' => false,
                    'expanded' => false,
                    'attr' => array(
                        "class"=> 'pi_multiselect',
                    ),
                    'label_attr' => [
                        'class'=> 'permission_collection',
                    ],
                ]);

                $form->add('metadata', Type\HiddenType::class, [
                    'required' => false,
                    'data'     => json_encode($options['metadata'], true),
                ]);
            },
            50
        );
    }

//    /**
//     * @param FormBuilderInterface $builder
//     * @param array $options
//     */
//    protected function setPostSubmit(FormBuilderInterface $builder, array $options)
//    {
//        $handler = $this->storageProviderHandler;
//        $validator = $this->validator;
//        $builder->addEventListener(
//            FormEvents::POST_SUBMIT,
//            function(FormEvent $event) use ($handler, $validator, $options) {
//                print_r('coinconi');exit;
//                $form = $event->getForm();
//                if (!is_null($validator)) {
//                    $violations = $validator->validate($form);
//                    if (count($violations) > 0) {
//                        return false;
//                    }
//                }
//
//                $media = $form->getData();
//                if (null === $media) {
//                    return false;
//                }
//            }
//        );
//    }
}
