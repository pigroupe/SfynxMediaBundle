<?php

namespace Sfynx\MediaBundle\Layers\Application\Validation\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;

//use Sonata\MediaBundle\Form\DataTransformer\ProviderDataTransformer;

class MediaType extends AbstractType
{
    protected $pool;

    protected $class;

    /**
     * @param Pool   $pool
     * @param string $class
     */
    public function __construct(/*Pool $pool*/)
    {
        //$this->pool  = $pool;
        $this->class = "Sfynx\MediaBundle\Layers\Domain\Entity\Media";
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->setAttribute('label', $options['label'])
        ->setAttribute('attr', $options['attr'])
        ->setAttribute('label_attr', $options['label_attr'])
//        ->addModelTransformer(new ProviderDataTransformer($this->pool, $this->class, array(
//            'provider' => $options['provider'],
//            'context'  => $options['context'],
//            'empty_on_new'  => $options['empty_on_new'],
//            'new_on_update' => $options['new_on_update'],
//        )))
        ;

        $builder->addEventListener(FormEvents::SUBMIT, function(FormEvent $event) {
            if ($event->getForm()->get('unlink')->getData()) {
                $event->setData(null);
            }
        });

//        $this->pool->getProvider($options['provider'])->buildMediaType($builder);

        $builder->add('unlink', 'checkbox', array(
            'mapped'   => false,
            'data'     => false,
            'required' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['provider'] = $options['provider'];
        $view->vars['context'] = $options['context'];
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->class,
            'provider'   => null,
            'context'    => null,
            'empty_on_new'  => true,
            'new_on_update' => true,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'form';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sfynx_related_to_one_media';
    }
}
