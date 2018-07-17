<?php

/**
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @license: MIT
 */

namespace Sfynx\MediaBundle\Layers\Application\Validation\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type as Types;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RelatedToManyMediaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'type'               => 'related_to_one_media',
            'options'            => array('required' => false,),
            'allow_add'          => true,
            'allow_delete'       => true,
            'by_reference'       => false,
            'attr'               => array(
                'class' => sprintf('tms_media_client__%s', $this->getName())
            )
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return Types\CollectionType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sfynx_related_to_many_media';
    }

    // For Symfony 2.x
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
