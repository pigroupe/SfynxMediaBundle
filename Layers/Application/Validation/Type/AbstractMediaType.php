<?php
namespace Sfynx\MediaBundle\Layers\Application\Validation\Type;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
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
            ->add('publicUri', HiddenType::class, [
                'required' => false
            ])
            ->add('mimeType', HiddenType::class, [
                'required' => false
            ])
            ->add('extension', HiddenType::class, [
                'required' => false
            ])
            ->add('providerReference', HiddenType::class, [
                'required' => false
            ])
            ->add('providerName', HiddenType::class, [
                'data'  => $options['storage_provider'],
            ])
            ->add('sourceName', HiddenType::class, [
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

                if (method_exists($parent, 'getTitle')) {
                    $form->add('title', HiddenType::class, [
                        'required' => false,
                        'data'  => $parent->getTitle(),
                    ]);
                }
                if (method_exists($parent, 'getDescriptif')) {
                    $form->add('descriptif', HiddenType::class, [
                        'required' => false,
                        'data'  => $parent->getDescriptif(),
                    ]);
                }

                if (null !== $event->getData()) {
                    $isUploadedFileRequired = false;
                }

                $form->add('uploadedFile', FileType::class, [
                    'label' => ' ',
                    'mapped' => false,
                    'required' => $isUploadedFileRequired,
                    'constraints' => $options['constraints']
                ]);

                $form->add('metadata', HiddenType::class, [
                    'required' => false,
                    'data'     => json_encode($options['metadata'], true),
                ]);
            },
            50
        );
    }
}
