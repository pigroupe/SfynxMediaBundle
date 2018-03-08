<?php
namespace Sfynx\MediaBundle\Layers\Domain\EventSubscriber;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\EventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Sfynx\CoreBundle\Layers\Infrastructure\EventListener\abstractListener;
use Sfynx\ToolBundle\Util\PiStringManager;
use Sfynx\MediaBundle\Layers\Domain\Entity\Interfaces\MediathequeInterface;
use Sfynx\MediaBundle\Layers\Domain\Entity\Interfaces\MediaInterface;
use Sfynx\MediaBundle\Layers\Domain\Entity\Media;
use Sfynx\MediaBundle\Layers\Domain\Entity\Mediatheque;
use Sfynx\MediaBundle\Layers\Domain\Service\StorageProvider\Generalisation\Interfaces\StorageProviderInterface;

/**
 * Storage Provider Handler class to upload files
 *
 * @category   Sfynx\MediaBundle\Layers
 * @package    Domain
 * @subpackage EventSubscriber
 * @author   Etienne de Longeaux <etienne.delongeaux@gmail.com>
 */
class StorageProviderHandler  extends abstractListener implements EventSubscriber
{
    /** @var string */
    protected $providerName;
    /** @var array */
    protected $storageProviders = [];

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::prePersist,
            Events::preUpdate,
            Events::preRemove,
        );
    }

    /**
     * Get StorageProvider
     *
     * @param string $serviceName
     * @return StorageProviderInterface|null
     */
    public function getStorageProvider($serviceName)
    {
        return isset($this->storageProviders[$serviceName]) ?
            $this->storageProviders[$serviceName] :
            null
            ;
    }

    /**
     * Add StorageProvider
     *
     * @param StorageProviderInterface $provider
     * @param string $serviceName
     */
    public function addStorageProvider(StorageProviderInterface $provider, $serviceName)
    {
        $this->storageProviders[$serviceName] = $provider;
    }

    /**
     * Constructor
     *
     * @param string $providerName
     * @param ContainerInterface $container
     */
    public function __construct(string $providerName, ContainerInterface $container)
    {
        $this->providerName = $providerName;
        parent::__construct($container);

//        if (class_exists('Sfynx\MediaBundle\Layers\Domain\Entity\Translation\MediathequeTranslation')) {
//            $this->addAssociation('Sfynx\MediaBundle\Layers\Domain\Entity\Mediatheque', 'mapOneToMany', array(
//                'fieldName'     => 'translations',
//                'targetEntity'  => 'Sfynx\MediaBundle\Layers\Domain\Entity\Translation\MediathequeTranslation',
//                'cascade'       => array(
//                    'persist',
//                    'remove',
//                ),
//                'mappedBy'      => 'object',
//                'orderBy'       => array(
//                    'locale'  => 'ASC',
//                ),
//            ));
//            $this->addAssociation('Sfynx\MediaBundle\Layers\Domain\Entity\Translation\MediathequeTranslation', 'mapManyToOne', array(
//                'fieldName'     => 'object',
//                'targetEntity'  => 'Sfynx\MediaBundle\Layers\Domain\Entity\Mediatheque',
//                'cascade'       => array(),
//                'inversedBy'    => 'translations',
//                'joinColumns'   =>  array(
//                    array(
//                        'name'  => 'object_id',
//                        'referencedColumnName' => 'id',
//                        'onDelete' => 'CASCADE'
//                    ),
//                ),
//            ));
//        }
//
//        if (class_exists('Sfynx\MediaBundle\Layers\Domain\Entity\Media')) {
//            $this->addAssociation('Sfynx\MediaBundle\Layers\Domain\Entity\Mediatheque', 'mapManyToOne', array(
//                'fieldName'     => 'image',
//                'targetEntity'  => 'Sfynx\MediaBundle\Layers\Domain\Entity\Media',
//                'cascade'       => array(
//                    'all',
//                ),
//                'joinColumns'   =>  array(
//                    array(
//                        'name'  => 'media',
//                        'referencedColumnName' => 'id',
//                        'nullable' => true
//                    ),
//                ),
//                'orphanRemoval' => false,
//            ));
//            $this->addAssociation('Sfynx\MediaBundle\Layers\Domain\Entity\Mediatheque', 'mapManyToOne', array(
//                'fieldName'     => 'image2',
//                'targetEntity'  => 'Sfynx\MediaBundle\Layers\Domain\Entity\Media',
//                'cascade'       => array(
//                    'all',
//                ),
//                'joinColumns'   =>  array(
//                    array(
//                        'name'  => 'media2',
//                        'referencedColumnName' => 'id',
//                        'nullable' => true
//                    ),
//                ),
//                'orphanRemoval' => false,
//            ));
//        }
    }

    /**
     * @param \Doctrine\Common\EventArgs $args
     * @return void
     */
    protected function recomputeSingleEntityChangeSet(EventArgs $args)
    {
        $em = $args->getEntityManager();

        $em->getUnitOfWork()->recomputeSingleEntityChangeSet(
            $em->getClassMetadata(get_class($args->getEntity())),
            $args->getEntity()
        );
    }

    /**
     * @param EventArgs $args
     *
     * @return void
     */
    public function prePersist(EventArgs $eventArgs)
    {
        $this->addMedia($eventArgs);
    }

    /**
     * @param EventArgs $args
     *
     * @return void
     */
    public function preUpdate(EventArgs $eventArgs)
    {
        $this->addMedia($eventArgs);
        $this->deleteMediatheque($eventArgs);
        $this->cropImage($eventArgs);
    }

    /**
     * {@inheritdoc}
     */
    public function preRemove(LifecycleEventArgs $eventArgs)
    {
        $this->deleteMedia($eventArgs);
    }

    /**
     * We are setting the Mediatheque image to null if removing the Media was checked.
     *
     * @param object $eventArgs
     */
    protected function deleteMediatheque($eventArgs)
    {
        $entity = $eventArgs->getEntity();
        if ($this->tokenStorage->isUsernamePasswordToken()
            && $entity instanceof MediathequeInterface
            && !$this->isRestrictionByRole($entity)
            && ($entity->getMediadelete() == true)
        ) {
//            $entity_table = $this->getOwningTable($eventArgs, $entity);
//            $query = "UPDATE $entity_table mytable SET mytable.media = null WHERE mytable.id = ?";
//            $this->_connexion($eventArgs)->executeUpdate($query, [$entity->getId()]);
            $this->deleteMedia($eventArgs, $entity->getImage());
            $entity->setImage(null);
        }
    }

    /**
     * We are deleting a media.
     *
     * @param object $eventArgs
     */
    protected function addMedia($eventArgs, $media = null)
    {
        $entity = $eventArgs->getEntity();

        if ($media instanceof MediaInterface) {
            $entity = $media;
        }

        if ($entity instanceof MediaInterface) {
            $provider = $this->getStorageProvider($this->providerName);
            $provider->add($entity);
        }
    }

    /**
     * We are deleting a media.
     *
     * @param object $eventArgs
     */
    protected function deleteMedia($eventArgs, $media = null)
    {
        $entity = $eventArgs->getEntity();
        if ($media instanceof MediaInterface) {
            $entity = $media;
        }
        if ($entity instanceof MediaInterface) {
            $provider = $this->getStorageProvider($this->providerName);
            $provider->remove($entity);
        }
    }

    /**
     * We return the clean of a string.
     *
     * @param string $string
     *
     * @return string name
     * @access private
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    protected function _cleanName($string)
    {
        $string = PiStringManager::minusculesSansAccents($string);
        $string = PiStringManager::cleanFilename($string);

        return $string;
    }

    /**
     * We link the entity widget type to the page.
     *
     * @param object $eventArgs
     *
     * @return void
     * @access protected
     * @final
     * @author Riad HELLAL <hellal.riad@gmail.com>
     */
    protected function cropImage($eventArgs)
    {
        if ($this->container->get('request_stack')->getCurrentRequest()
            && isset($_SERVER['REQUEST_URI'])
            && !empty($_SERVER['REQUEST_URI'])
        ) {
            $entityManager = $eventArgs->getEntityManager();
            $tab_post = $this->container->get('request_stack')->getCurrentRequest()->request->all();
            if (!empty($tab_post['img_crop']) && $tab_post['img_crop'] == '1') {
                    $entity = $eventArgs->getEntity();
                    $getMedia = "getMedia";
                    $setMedia = "setMedia";
                    if ($this->tokenStorage->isUsernamePasswordToken()
                        && method_exists($entity, $getMedia)
                        && method_exists($entity, $setMedia)
                        && ($entity->$getMedia() instanceof MediathequeInterface)
                    ) {
                            $mediaPath = $this->container->get('sonata.media.twig.extension')->path($entity->$getMedia()->getImage()->getId(), 'reference');
                            $src = $this->container->get('kernel')->getRootDir() . '/../web/' . $mediaPath;
                            if (file_exists($src)) {
                                    $extension =  pathinfo($src, PATHINFO_EXTENSION);
                                    $mediaCrop = $this->container->get('sonata.media.twig.extension')->path($entity->$getMedia()->getImage()->getId(), $tab_post['img_name']);
                                    $targ_w = $tab_post['img_width']; //$globals['tailleWidthEdito1'];
                                    $targ_h = $tab_post['img_height'];
                                    $jpeg_quality = $tab_post['img_quality'];
                                    switch ($extension) {
                                            case 'jpg':
                                                    $img_r = imagecreatefromjpeg($src);
                                                    break;
                                            case 'jpeg':
                                                    $img_r = imagecreatefromjpeg($src);
                                                    break;
                                            case 'gif':
                                                    $img_r = imagecreatefromgif($src);
                                                    break;
                                            case 'png':
                                                    $img_r = imagecreatefrompng($src);
                                                    break;
                                            default:
                                                    echo "L'image n'est pas dans un format reconnu. Extensions autorisÃ©es : jpg, jpeg, gif, png";
                                                    break;
                                    }
                                    $dst_r = imagecreatetruecolor($targ_w, $targ_h);
                                    imagecopyresampled($dst_r, $img_r, 0, 0, $tab_post['x'], $tab_post['y'], $targ_w, $targ_h, $tab_post['w'], $tab_post['h']);
                                    switch ($extension) {
                                            case 'jpg':
                                                    imagejpeg($dst_r, $this->container->get('kernel')->getRootDir() . '/../web/' . $mediaCrop, $jpeg_quality);
                                                    break;
                                            case 'jpeg':
                                                    imagejpeg($dst_r, $this->container->get('kernel')->getRootDir() . '/../web/' . $mediaCrop, $jpeg_quality);
                                                    break;
                                            case 'gif':
                                                    imagegif($dst_r, $this->container->get('kernel')->getRootDir() . '/../web/' . $mediaCrop);
                                                    break;
                                            case 'png':
                                                    imagepng($dst_r, $this->container->get('kernel')->getRootDir() . '/../web/' . $mediaCrop);
                                                    break;
                                            default:
                                                    echo "L'image n'est pas dans un format reconnu. Extensions autorisÃ©es : jpg, gif, png";
                                                    break;
                                    }
                                    @chmod($this->container->get('kernel')->getRootDir() . '/../web/' . $mediaCrop, 0777);
                            }
                    }
            } elseif(!empty($tab_post['img_crop']) && count($tab_post['img_crop']) >= 1){
                if ($this->tokenStorage->isUsernamePasswordToken() ) {
                    foreach ($tab_post['img_crop'] as $media_id => $value) {
                        if ($value == 1) {
                            $mediaPath = $this->container->get('sonata.media.twig.extension')->path($media_id, 'reference');
                            $src = $this->container->get('kernel')->getRootDir() . '/../web/' . $mediaPath;
                            if (file_exists($src)) {
                                $extension =  pathinfo($src, PATHINFO_EXTENSION);
                                $mediaCrop = $this->container->get('sonata.media.twig.extension')->path($media_id, $tab_post['img_name_'.$media_id]);
                                $targ_w = $tab_post['img_width_'.$media_id]; //$globals['tailleWidthEdito1'];
                                $targ_h = $tab_post['img_height_'.$media_id];
                                $jpeg_quality = $tab_post['img_quality_'.$media_id];
                                switch ($extension) {
                                    case 'jpg':
                                        $img_r = imagecreatefromjpeg($src);
                                        break;
                                    case 'jpeg':
                                        $img_r = imagecreatefromjpeg($src);
                                        break;
                                    case 'gif':
                                        $img_r = imagecreatefromgif($src);
                                        break;
                                    case 'png':
                                        $img_r = imagecreatefrompng($src);
                                        break;
                                    default:
                                        echo "L'image n'est pas dans un format reconnu. Extensions autorisÃ©es : jpg, jpeg, gif, png";
                                        break;
                                }
                                $dst_r = imagecreatetruecolor($targ_w, $targ_h);
                                imagecopyresampled($dst_r, $img_r, 0, 0, $tab_post['x_'.$media_id], $tab_post['y_'.$media_id], $targ_w, $targ_h, $tab_post['w_'.$media_id], $tab_post['h_'.$media_id]);
                                switch ($extension) {
                                    case 'jpg':
                                        imagejpeg($dst_r, $this->container->get('kernel')->getRootDir() . '/../web/' . $mediaCrop, $jpeg_quality);
                                        break;
                                    case 'jpeg':
                                        imagejpeg($dst_r, $this->container->get('kernel')->getRootDir() . '/../web/' . $mediaCrop, $jpeg_quality);
                                        break;
                                    case 'gif':
                                        imagegif($dst_r, $this->container->get('kernel')->getRootDir() . '/../web/' . $mediaCrop);
                                        break;
                                    case 'png':
                                        imagepng($dst_r, $this->container->get('kernel')->getRootDir() . '/../web/' . $mediaCrop);
                                        break;
                                    default:
                                        echo "L'image n'est pas dans un format reconnu. Extensions autorisÃ©es : jpg, gif, png";
                                        break;
                                }
                                @chmod($this->container->get('kernel')->getRootDir() . '/../web/' . $mediaCrop, 0777);
                            }
                        }
                    } // endforeach
                }
            }
    	}
    }
}
