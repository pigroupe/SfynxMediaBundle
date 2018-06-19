<?php
namespace Sfynx\MediaBundle\Layers\Application\Validation\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Sfynx\ToolBundle\Builder\RouteTranslatorFactoryInterface;
use PiApp\GedmoBundle\Layers\Domain\Entity\Category;
use Sfynx\MediaBundle\Layers\Application\Validation\Type\RelatedToOneMediaType;
use Sfynx\CoreBundle\Layers\Application\Validation\Type\AbstractType;
use Sfynx\CoreBundle\Layers\Domain\Service\Manager\Generalisation\Interfaces\ManagerInterface;

/**
 * Description of the MediaType form.
 *
 * @category   Sfynx\MediaBundle\Layers
 * @package    Application
 * @subpackage Validation\Type
 * @author   Etienne de Longeaux <etienne.delongeaux@gmail.com>
 */
class MediathequeType extends AbstractType
{
    /** @var TranslatorInterface */
    protected $translator;
    /** @var RouteTranslatorFactoryInterface */
    protected $routeFactory;
    /** @var string */
    protected $_status;
    /** @var string */
    protected $_class;
    /** @var string */
    protected $_simpleLink;
    /** @var string */
    protected $_labelLink;
    /** @var string */
    protected $_context;

    /**
     * Constructor.
     * @param ManagerInterface $manager
     * @param RouteTranslatorFactoryInterface $routeFactory
     * @param TranslatorInterface $translator
     * @param string $status    ['file', 'image', 'youtube', 'dailymotion']
     * @return void
     */
    public function __construct(
        ManagerInterface $manager,
        RouteTranslatorFactoryInterface $routeFactory,
        TranslatorInterface $translator,
        $status = '',
        $simpleLink = "all",
        $labelLink = "",
        $context = "",
        $class =  "media_collection"
    ) {
        parent::__construct($manager);

        $this->routeFactory = $routeFactory;
        $this->translator = $translator;

        $this->_status       = $status;
        $this->_class        = $class;
        $this->_simpleLink   = $simpleLink;
        $this->_labelLink    = $labelLink;
        $this->_context      = $context;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $id_category = $this->data_form['id_category'];
        $categories = $this->data_form['categories'];

        if (isset($this->data_form['status'])) {
            $this->_status = $this->data_form['status'];
        }

        $builder
                ->add('status', HiddenType::class, [
                    "data"        => $this->_status,
                    "label_attr" => array(
                            "class"=> $this->_class,
                    ),
                    'required'  => false,
                ])
//                ->add('updated_at', null, array(
//                    'widget' => 'single_text', // choice, text, single_text
//                    'attr'=>array('style'=>'display:none;'),
//                    "label_attr" => array(
//                        "style"=> 'display:none;',
//                    ),
//                ))
        ;

        if ($this->_simpleLink == "all") {
            $builder
                 ->add('enabled', CheckboxType::class, [
                    //'data'  => true,
                     'label'    => 'pi.form.label.field.enabled',
                     "label_attr" => array(
                         "class"=> $this->_class,
                     ),
                ])
                 ->add('category', EntityType::class, [
                    'class' => 'PiAppGedmoBundle:Category',
                    'query_builder' => function (EntityRepository $er) use ($categories) {
                         return $categories;
                    },
//                    'placeholder' => 'pi.form.label.select.choose.category',
                    'choice_value' => 'id',
                    'label'    => "pi.form.label.field.category",
                    'multiple'    => false,
                    'required'  => false,
                    "attr" => array(
                        "class"=>"pi_simpleselect ajaxselect", // ajaxselect
                        "data-url" => $this->routeFactory->generate("admin_gedmo_category_selectentity_ajax", array('type'=> Category::TYPE_MEDIA)),
                        "data-selectid" => $id_category,
                        "data-max" => 50,
                    ),
                    'widget_suffix' => '<a class="button-ui-mediatheque button-ui-dialog"
                                    title="Ajouter une catégorie"
                                    data-title="Catégorie"
                                    data-href="' . $this->routeFactory->generate("admin_gedmo_category_new", array("NoLayout"=>"false", 'type'=> 2)) . '"
                                    data-selectid="#piapp_gedmobundle_categorytype_id"
                                    data-selecttitle="#piapp_gedmobundle_categorytype_name"
                                    data-insertid="#sfynx_mediabundle_mediatype_image_category"
                                    data-inserttype="multiselect"
                                    ></a>',
                ])
                 ->add('title', TextType::class, [
                    'label'      => "pi.form.label.field.title",
                    "label_attr" => [
                        "class" => $this->_class,
                    ],
                    'required' => true
                 ])
                 ->add('descriptif', TextareaType::class, [
                    'label'    => 'pi.form.label.field.description',
                    "label_attr" => array(
                        "class"=>"content_collection",
                    ),
                 ])
                 ->add('url', TextType::class, [
                    "label"     => "pi.form.label.field.url",
                    "label_attr" => [
                        "class"=> $this->_class,
                    ],
                    'required'  => false
                 ])
            ;
        } elseif ($this->_simpleLink == "simpleCategory") {
            $builder
                ->add('enabled', HiddenType::class, [
                    'data'  => true,
                     "label_attr" => [
                         "class"=> $this->_class,
                     ],
                ])
                ->add('category', EntityType::class, [
                    'class' => 'PiAppGedmoBundle:Category',
                    'query_builder' => function (EntityRepository $er) use ($categories) {
                        return $categories;
                    },
                    'placeholder' => 'pi.form.label.select.choose.category',
                    'choice_value' => 'id',
                    'label'    => "pi.form.label.field.category",
                    'multiple'    => false,
                    'required'  => false,
                    "attr" => array(
                        "class" => "pi_simpleselect ajaxselect", // ajaxselect
                        "data-url" => $this->routeFactory->generate("admin_gedmo_category_selectentity_ajax", array('type'=> Category::TYPE_MEDIA)),
                        "data-selectid" => $id_category,
                        "data-max" => 50,
                    ),
                    'widget_suffix' => '<a class="button-ui-mediatheque button-ui-dialog"
                                    title="Ajouter une catégorie"
                                    data-title="Catégorie"
                                    data-href="'.$this->routeFactory->generate("admin_gedmo_category_new", array("NoLayout"=>"false", 'type'=> Category::TYPE_MEDIA)).'"
                                    data-selectid="#piapp_gedmobundle_categorytype_id"
                                    data-selecttitle="#piapp_gedmobundle_categorytype_name"
                                    data-insertid="#piapp_gedmobundle_slidertype_category"
                                    data-inserttype="multiselect"
                                    ></a>',
                ])
            ;
        } elseif ( ($this->_simpleLink == "simpleDescriptif") || ($this->_simpleLink == "simpleWithIcon") ) {
        	$builder
        	->add('enabled', HiddenType::class, [
        			'data'  => true,
        			"label_attr" => array(
                        "class"=> $this->_class,
        			),
        	])
        	->add('title', TextType::class, [
        			'label'            => "pi.form.label.field.title",
        			"label_attr" => array(
                        "class"=> $this->_class,
        			),
        			'required' => true,
        	])
        	->add('descriptif', TextareaType::class, [
        			'label'    => 'pi.form.label.field.description',
        			"label_attr" => array(
        					"class"=>"content_collection",
        			),
        	])
        	;
        } elseif ($this->_simpleLink == "crop") {
        	$builder
        	->add('enabled', HiddenType::class, [
        			'data'  => true,
                "label_attr" => [
                    "class"=> $this->_class,
                ],
        	])
        	->add('title', TextType::class, [
                'label'      => "pi.form.label.field.title",
                "label_attr" => [
                    "class"=> $this->_class,
                ],
                'required'    => true,
        	])
        	;
        } elseif (($this->_simpleLink == "simpleLink")
            || ($this->_simpleLink == "hidden")
            || ($this->_simpleLink == "simple")
        ) {
            $builder
            ->add('enabled', HiddenType::class, [
                    'data'  => true,
                     "label_attr" => [
                         "class"=> $this->_class,
                     ],
                ])
            ;
        }
        $style = "";
        if ($this->_simpleLink == "hidden") {
            $style = "display:none";
        }
        if ($this->_status == "file") {
            if ($this->_labelLink == "")  $this->_labelLink = 'pi.form.label.media.file';
            if ($this->_context == "")    $this->_context   = 'default';
             $builder->add('image', RelatedToOneMediaType::class, [
                     'storage_provider' => 'gaufrette_storage_gallery_azure',
                     'storage_source' => 'mediatheque/file',
                     'handler' => 'sfynx.media.provider.file',
                     'metadata' => [
                         'form_name' => 'sfynx_mediabundle_mediatype_file',
                         'field_form' => 'image'
                     ],
                     'context' => $this->_context,
                     'label' => $this->_labelLink,
                     "label_attr" => array(
                         "class"=> $this->_class,
                         "style"=> $style,
                     ),
                     "attr"     => ["style"=> $style],
                     'required' => false,
             ]);
         } elseif ($this->_status == "picture") {
             if ($this->_labelLink == "") $this->_labelLink = 'pi.form.label.media.picture';
             if ($this->_context == "")    $this->_context  = 'default';
             $builder->add('image', RelatedToOneMediaType::class, [
                     'storage_provider' => 'gaufrette_storage_gallery_azure',
                     'storage_source' => 'mediatheque/picture',
                     'handler'     => 'sfynx.media.provider.picture',
                     'metadata' => [
                         'form_name' => 'sfynx_mediabundle_mediatype_picture',
                         'field_form' => 'image'
                     ],
                     'context'      => $this->_context,
                     'label'        => $this->_labelLink,
                     "label_attr" => [
                         "class"=> $this->_class,
                         "style"=> $style,
                     ],
                     "attr"    => ["style"=> $style],
                     'required'  => false,
             ]);
             if ($this->_simpleLink == "simpleWithIcon") {
             	if ($this->_labelLink == "") $this->_labelLink = 'miniature';
             	if ($this->_context == "")    $this->_context        = 'default';
             	$builder->add('image2', RelatedToOneMediaType::class, [
                        'storage_provider' => 'gaufrette_storage_gallery_azure',
                        'storage_source' => 'mediatheque/picture',
             	        'handler' => 'sfynx.media.provider.picture',
                        'metadata' => [
                            'form_name' => 'sfynx_mediabundle_mediatype_picture',
                            'field_form' => 'image'
                        ],
             			'context' => $this->_context,
             			'label' => "pi.form.label.media.picture.miniature",
             			"label_attr" => [
                            "class"=> $this->_class,
                            "style"=> $style,
             			],
             			"attr"    => ["style"=> $style],
             			'required'  => false,
             	]);
             }
         } elseif ($this->_status == "youtube") {
             if ($this->_labelLink == "") $this->_labelLink = 'pi.form.label.media.youtube';
             if ($this->_context == "")   $this->_context   = 'default';
             $builder->add('image', RelatedToOneMediaType::class, [
                     'storage_provider' => '',
                     'storage_source' => '',
                     'handler' => 'sfynx.media.provider.youtube',
                     'context' => $this->_context,
                     'label' => $this->_labelLink,
                     "label_attr" => [
                         "class"=> $this->_class,
                         "style"=> $style,
                     ],
                     "attr"     => ["style"=> $style],
                     'required' => false,
             ]);
         } elseif ($this->_status == "dailymotion") {
             if ($this->_labelLink == "") $this->_labelLink = 'pi.form.label.media.dailymotion';
             if ($this->_context == "")   $this->_context   = 'default';
             $builder->add('image', RelatedToOneMediaType::class, [
                     'storage_provider' => '',
                     'storage_source' => '',
                     'handler' => 'sfynx.media.provider.dailymotion',
                     'context' => $this->_context,
                     'label' => $this->_labelLink,
                     "label_attr" => [
                         "class"=> $this->_class,
                         "style"=> $style,
                     ],
                     "attr" => ["style"=> $style],
                     'required' => false,
              ]);
         }

         if (($this->_simpleLink != "hidden") && ($this->_simpleLink != "simple") && ($this->_simpleLink != "crop")) {
             $builder
                 ->add('mediadelete', CheckboxType::class, [
                     'data'  => false,
                     'required'  => false,
                     'help_block' => $this->translator->trans('pi.media.form.field.mediadelete', ['%s'=>$this->_status]),
                     'label'      => "pi.delete",
                     "label_attr" => [
                         "class" => $this->_class,
                     ],
                     "attr" => ["style"=> $style],
                 ]);
         }

         $builder
         ->add('copyright', TextType::class, [
         		"label"     => "Crédit photo",
         		"label_attr" => array(
                    "class"=> $this->_class,
         		),
         		'required' => false,
         ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->data_class,
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        if (isset($this->data_form['status'])) {
            $this->_status = $this->data_form['status'];
        }
        return 'sfynx_mediabundle_mediatype_' . $this->_status;
    }

    // For Symfony 2.x
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
