<?php
namespace Sfynx\MediaBundle\Layers\Application\Validation\Type;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Sfynx\MediaBundle\Layers\Application\Validation\Type\AbstractMediaType;

/**
 * Description of the RelatedToOneMediaType form.
 *
 * @category   Sfynx\MediaBundle\Layers
 * @package    Application
 * @subpackage Validation\Type
 * @author   Etienne de Longeaux <etienne.delongeaux@gmail.com>
 */
class RelatedToOneMediaType extends AbstractMediaType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $this->setPreSetData($builder, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['storage_provider'] = $options['storage_provider'];
        $view->vars['storage_source'] = $options['storage_source'];
        $view->vars['handler'] = $options['handler'];
        $view->vars['context'] = $options['context'];
        $view->vars['metadata'] = $options['metadata'];
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sfynx_related_to_one_media';
    }

    // For Symfony 2.x
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
