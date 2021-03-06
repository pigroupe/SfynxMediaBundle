<?php
namespace Sfynx\MediaBundle\Layers\Domain\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Sfynx\PositionBundle\Annotation as PI;
use Sfynx\CoreBundle\Layers\Domain\Model\Traits;
use Sfynx\CoreBundle\Layers\Domain\Model\AbstractDefault;
use Sfynx\MediaBundle\Layers\Domain\Entity\Interfaces\MediathequeInterface;
use Sfynx\MediaBundle\Layers\Domain\Entity\AddTrait;
use Sfynx\MediaBundle\Layers\Domain\Entity\Media;

use Sfynx\DoctrineRestDriver\Layers\Application\Routing\Annotations as DataSource;

/**
 * Sfynx\MediaBundle\Layers\Domain\Entity\Mediatheque
 *
 * @ORM\MappedSuperclass
 * @ORM\Table(name="sfynx_mediatheque")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 * @Gedmo\TranslationEntity(class="Sfynx\MediaBundle\Layers\Domain\Entity\Translation\MediathequeTranslation")
 *
 * @category   SonataMedia
 * @package    Entity
 * @subpackage Model
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2015 PI-GROUPE
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    2.3
 * @link       http://opensource.org/licenses/gpl-license.php
 * @since      2015-02-16
 *
 *
 * @DataSource\Select("http://dildevelop-demoapi-orm.alterway.dev/api/v1/actors.json/{id}", method="GET", statusCode=200, options={"X-TENANT-ID"="000001"})
 * @DataSource\Fetch("http://dildevelop-demoapi-orm.alterway.dev/api/v1/actors.json", method="GET", statusCode=200, options={"X-TENANT-ID"="000001"})
 * @DataSource\SearchBy("http://dildevelop-demoapi-orm.alterway.dev/api/v1/actor/searchby.json", method="GET", statusCode=200, options={"X-TENANT-ID"="000001"})
 * @DataSource\Insert("http://dildevelop-demoapi-orm.alterway.dev/api/v1/actors.json", method="POST", statusCode=200, options={"X-TENANT-ID"="000001"})
 * @DataSource\Update("http://dildevelop-demoapi-orm.alterway.dev/api/v1/actors.json/{id}", method="PUT", statusCode=200, options={"X-TENANT-ID"="000001"})
 * @DataSource\Patch("http://dildevelop-demoapi-orm.alterway.dev/api/v1/actors.json/{id}", method="PATCH", statusCode=200, options={"X-TENANT-ID"="000001"})
 * @DataSource\Delete("http://dildevelop-demoapi-orm.alterway.dev/api/v1/actors.json/{id}", method="DELETE", statusCode=200, options={"X-TENANT-ID"="000001"} )
 */
class Mediatheque extends AbstractDefault implements MediathequeInterface
{
    use Traits\TraitBuild;
    use Traits\TraitEnabled;
    use Traits\TraitDatetime;
    use Traits\TraitHeritage;

    /**
     * List of all translatable fields
     *
     * @var array
     * @access  protected
     */
    protected $_fields    = array('title', 'descriptif');

    /**
     * Name of the Translation Entity
     *
     * @var array
     * @access  protected
    */
    protected $_translationClass = 'Sfynx\MediaBundle\Layers\Domain\Entity\Translation\MediathequeTranslation';

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Sfynx\MediaBundle\Layers\Domain\Entity\Translation\MediathequeTranslation", mappedBy="object", cascade={"persist", "remove"})
     */
    protected $translations;

    /**
     * @var bigint
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var \PiApp\GedmoBundle\Layers\Domain\Entity\Category $category
     *
     * @ORM\ManyToOne(targetEntity="PiApp\GedmoBundle\Layers\Domain\Entity\Category", inversedBy="items_media")
     * @ORM\JoinColumn(name="category", referencedColumnName="id", nullable=true)
     */
    protected $category;

    /**
     * @var string $status
     *
     * @ORM\Column(name="status", type="string", length=25, nullable=true)
     */
    protected $status;

    /**
     * @var string $title
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="title", type="string", length=255, nullable = true)
     */
    protected $title;

    /**
     * @var text $descriptif
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="descriptif", type="text", nullable=true)
     */
    protected $descriptif;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=314, nullable=true)
     */
    protected $url;

    /**
     * @var \Sfynx\MediaBundle\Layers\Domain\Entity\Media $image
     *
     * @ORM\ManyToOne(targetEntity="Sfynx\MediaBundle\Layers\Domain\Entity\Media", cascade={"all"})
     * @ORM\JoinColumn(name="media", referencedColumnName="id", nullable=true)
     */
    protected $image;

    /**
     * @var \Sfynx\MediaBundle\Layers\Domain\Entity\Media $image2
     *
     * @ORM\ManyToOne(targetEntity="Sfynx\MediaBundle\Layers\Domain\Entity\Media", cascade={"all"})
     * @ORM\JoinColumn(name="media2", referencedColumnName="id", nullable=true)
     */
    protected $image2;

    /**
     * @var boolean $mediadelete
     *
     * @ORM\Column(name="mediadelete", type="boolean", nullable=true)
     */
    protected $mediadelete;

    /**
     * @var string $copyright
     *
     * @ORM\Column(name="copyright", type="string", length=255, nullable=true)
     */
    protected $copyright;

    /**
     * @ORM\Column(name="position", type="integer",  nullable=true)
     * @PI\Positioned(SortableOrders = {"type":"relationship","field":"category","columnName":"category"})
     */
    protected $position;

    /**
     * Get magic method
     */
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }

    /**
     * Set magic method
     */
    public function __set($property, $value) 
    {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }

        return $this;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *
     * This method is used when you want to convert to string an object of
     * this Entity
     * ex: $value = (string)$entity;
     *
     */
    public function __toString()
    {
        if (isset($_GET['_locale']) && !empty($_GET['_locale'])) {
            $locale = $_GET['_locale'];
        } else {
            $locale = "fr_FR";
        }
        $content = $this->getId();
        $title = $this->translate($locale)->getTitle();
        $cat = $this->getCategory();
        if ($title) {
            $content .=  " - " .$title;
        }
        if (!(null === $cat)) {
            $content .=  ' ('. $cat->getName() .')';
        }
        if (($this->getStatus() == 'image')
                && ($this->getImage() instanceof Media)
        ) {
            $content .= "<img width='10px' src='".$this->getImage()->getUrl(null, [
                'maxAge' => 31536000,
                'resize' => 1,
                'width' => 100,
            ])."' alt='Photo'/>";
        }

        return (string) $content;
    }

    /**
     * Get id
     *
     * @return bigint
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set category
     *
     * @param \PiApp\GedmoBundle\Layers\Domain\Entity\Category $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Get category
     *
     * @return \PiApp\GedmoBundle\Layers\Domain\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set status
     *
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set descriptif
     *
     * @param text $descriptif
     */
    public function setDescriptif ($descriptif)
    {
        $this->descriptif = $descriptif;
        return $this;
    }

    /**
     * Get descriptif
     *
     * @return text
     */
    public function getDescriptif ()
    {
        return $this->descriptif;
    }

    /**
     * Set $url
     *
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Get $url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set image
     *
     * @param \Sfynx\MediaBundle\Layers\Domain\Entity\Media $image
     */
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * Get image
     *
     * @return Media
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set image2
     *
     * @param Media $image2
     */
    public function setImage2($image2)
    {
        $this->image2     = $image2;
        return $this;
    }

    /**
     * Get image2
     *
     * @return \Sfynx\MediaBundle\Layers\Domain\Entity\Media
     */
    public function getImage2()
    {
        return $this->image2;
    }

    /**
     * Set mediadelete
     *
     * @param boolean $mediadelete
     */
    public function setMediadelete($mediadelete)
    {
        $this->mediadelete = $mediadelete;
        return $this;
    }

    /**
     * Get mediadelete
     *
     * @return boolean
     */
    public function getMediadelete()
    {
        return $this->mediadelete;
    }

    /**
     * {@inheritdoc}
     */
    public function setCopyright($copyright)
    {
        $this->copyright = $copyright;
    }

    /**
     * {@inheritdoc}
     */
    public function getCopyright()
    {
        return $this->copyright;
    }
}
