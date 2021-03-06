<?php
namespace Sfynx\MediaBundle\Layers\Domain\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Util\Inflector;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Sfynx\CoreBundle\Layers\Domain\Model\Traits;
use Sfynx\MediaBundle\Layers\Domain\Entity\Interfaces\MediaInterface;

/**
 * Sfynx\MediaBundle\Layers\Domain\Entity\Mediatheque
 *
 * @ORM\MappedSuperclass
 * @ORM\Table(name="sfynx_media")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 * @Gedmo\TranslationEntity(class="Sfynx\MediaBundle\Layers\Domain\Entity\Translation\MediaTranslation")
 *
 * @category   Sfynx\MediaBundle\Layers
 * @package    Domain
 * @subpackage Entity
 *
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2015 PI-GROUPE
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version    2.3
 * @link       http://opensource.org/licenses/gpl-license.php
 * @since      2015-02-16
 */
class Media implements MediaInterface
{
    use Traits\TraitBuild;
    use Traits\TraitEnabled;
    use Traits\TraitDatetime;

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
    protected $_translationClass = 'Sfynx\MediaBundle\Layers\Domain\Entity\Translation\MediaTranslation';

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="name", type="string", nullable=true)
     */
    protected $name;

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
     * @ORM\Column(name="public_uri", type="string", nullable=true)
     */
    protected $publicUri = null;

    /**
     * @ORM\Column(name="mime_type", type="string", nullable=true)
     */
    protected $mimeType;

    /**
     * @var string
     */
    protected $providerStorage;

    /**
     * @ORM\Column(name="provider_name_storage", type="string", nullable=true)
     */
    protected $providerName;

    /**
     * @ORM\Column(name="provider_reference", type="string", nullable=true)
     */
    protected $providerReference;

    /**
     * @ORM\Column(name="provider_data", type="json_array", nullable=true)
     */
    protected $providerData;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $extension;

    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $quality = 95;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    protected $metadata = [];

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $sourceName;

    /**
     * @var UploadedFile
     */
    protected $uploadedFile;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", options={"default" = true})
     */
    protected $connected = false;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    protected $rangeIp = [];

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    protected $roles = [];

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    protected $usernames = [];

    /**
     * @return Media
     */
    public static function createFromNative(): self
    {
        return new self();
    }

    /**
     * toString.
     *
     * @return string
     */
    public function __toString()
    {
        return \sprintf('[%s] %s',
            $this->getProviderName(),
            $this->getProviderReference()
        );
    }

    /**
     * toArray.
     *
     * @return array
     */
    public function __toArray()
    {
        return [
            'enabled' => $this->enabled,
            'publicUri' => $this->publicUri,
            'mimeType' => $this->mimeType,
            'providerName' => $this->providerName,
            'providerReference' => $this->providerReference,
            'providerData' => $this->providerData,
            'extension' => $this->extension,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'metadata' => $this->metadata,
            'rangeIp' => $this->rangeIp,
            'roles' => $this->roles,
            'usernames' => $this->usernames,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return \serialize($this->__toArray());
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($data)
    {
        $unserializedData = \unserialize($data);

        $this->enabled = $unserializedData['enabled'];
        $this->usernames = $unserializedData['usernames'];
        $this->publicUri = $unserializedData['publicUri'];
        $this->mimeType = $unserializedData['mimeType'];
        $this->providerName = $unserializedData['providerName'];
        $this->providerReference = $unserializedData['providerReference'];
        $this->providerData = $unserializedData['providerData'];
        $this->extension = $unserializedData['extension'];
        $this->created_at = $unserializedData['created_at'];
        $this->updated_at = $unserializedData['updated_at'];
        $this->metadata = $unserializedData['metadata'];
        $this->rangeIp = $unserializedData['rangeIp'];
        $this->roles = $unserializedData['roles'];
        $this->usernames = $unserializedData['usernames'];
    }

//    /**
//     * Magic setter.
//     *
//     * @param string $name  The setter name.
//     * @param mixed  $value The value to set.
//     * @return $this
//     */
//    public function __set($name, $value)
//    {
//        $name = Inflector::tableize($name);
//        $this->setMetadata($name, $value);
//
//        return $this;
//    }
//
//    /**
//     * Magic getter.
//     *
//     * @param string $name The getter name.
//     * @return mixed The value.
//     */
//    public function __get($name)
//    {
//        $name = Inflector::tableize($name);
//
//        return $this->getMetadata($name);
//    }

    /**
     * Magic call.
     *
     * @param string $method    The method name.
     * @param mixed  $arguments The given arguments.
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        $property  = $method;
        $subMethod = \substr($method, 0, 3);
        if (\in_array($subMethod, ['set', 'get'])) {
            $property = Inflector::tableize(\substr($method, 3));
            if ('set' === $subMethod) {
                $this->$property = $arguments;
            }
        }

        return $this->$property;
    }

    /**
     * Get id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * isImageable
     */
    public function isImageable($withPdf = true)
    {
        if (null === $this->getPublicUri()) {
            return false;
        }

        if ($withPdf
            && 'application/pdf' === $this->getMimeType()
        ) {
            return true;
        }

        return (boolean)\preg_match("#^image/#", $this->getMimeType());
    }

    /**
     * Returns public data
     *
     * @return array
     */
    public function getPublicData()
    {
        return [
            'providerName' => $this->getProviderName(),
            'providerReference' => $this->getProviderReference(),
            'publicUri' => $this->getPublicUri(),
            'extension' => $this->getExtension(),
            'mimeType' => $this->getMimeType(),
            'enabled' => $this->getEnabled()
        ];
    }

    /**
     * Set uploaded file.
     *
     * @param UploadedFile $uploadedFile
     * @return $this
     */
    public function setUploadedFile(UploadedFile $uploadedFile)
    {
        $this->uploadedFile = $uploadedFile;
        return $this;
    }

    /**
     * Returns uploaded file.
     *
     * @return UploadedFile
     */
    public function getUploadedFile()
    {
        return $this->uploadedFile;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return $this
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
     * @return $this
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
     * Returns public uri
     *
     * @param string $publicUri
     * @return $this
     */
    public function setPublicUri($publicUri)
    {
        $this->publicUri = $publicUri;
        return $this;
    }

    /**
     * Get public uri.
     *
     * @return string
     */
    public function getPublicUri()
    {
        return $this->publicUri;
    }

    /**
     * Returns url.
     *
     * @param string|null $extension
     * @param array $query
     * @return string
     */
    public function getUrl(string $extension = null, array $query = []): string
    {
        $extension = (null === $extension) ? $this->getExtension() : $extension;

        return self::getUrlValue($this->getPublicUri(), $extension, $query);
    }

    /**
     * Returns url.
     *
     * @param string|null $extension
     * @param array $query
     * @param string|null $uri
     * @return string
     * @static
     */
    public static function getUrlValue(string $uri = null, string $extension = null, array $query = []): string
    {
        if (null === $uri) {
            return '';
        }

        $countValidQueries = 0;
        foreach ($query as $k => $param) {
            if (!$param) {
                unset($query[$k]);
            } else {
                $countValidQueries++;
            }
        }

        if (null !== $uri  && null !== $extension) {
            $uri = sprintf('%s.%s', $uri, $extension);
        }

        if ($countValidQueries == 0) {
            return $uri;
        }

        return sprintf('%s?%s', $uri, http_build_query($query));
    }

    /**
     * Set mimeType.
     *
     * @param string $mimeType
     * @return $this
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
        return $this;
    }

    /**
     * Returns mimeType.
     *
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * Set providerStorage.
     *
     * @param string $providerStorage
     * @return $this
     */
    public function setProviderStorage($providerStorage)
    {
        $this->providerStorage = $providerStorage;
        return $this;
    }

    /**
     * Returns providerStorage.
     *
     * @return string
     */
    public function getProviderStorage()
    {
        return $this->providerStorage;
    }

    /**
     * Set providerName.
     *
     * @param string $providerName
     * @return $this
     */
    public function setProviderName($providerName)
    {
        $this->providerName = $providerName;
        return $this;
    }

    /**
     * Returns providerName.
     *
     * @return string
     */
    public function getProviderName()
    {
        return $this->providerName;
    }

    /**
     * Set sourceName.
     *
     * @param string $sourceName
     * @return $this
     */
    public function setSourceName($sourceName)
    {
        $this->sourceName = $sourceName;
        return $this;
    }

    /**
     * Return the path upload file
     *
     * @return string
     */
    public function getSourceName()
    {
        return $this->sourceName;
    }

    /**
     * Set providerReference.
     *
     * @param string $providerReference
     * @return $this
     */
    public function setProviderReference($providerReference)
    {
        $this->providerReference = $providerReference;
        return $this;
    }

    /**
     * Returns providerReference.
     *
     * @return string
     */
    public function getProviderReference()
    {
        return $this->providerReference;
    }

    /**
     * Set providerData.
     *
     * @param array $providerData
     * @return $this
     */
    public function setProviderData($providerData)
    {
        $this->providerData = $providerData;
        return $this;
    }

    /**
     * Returns providerData.
     *
     * @return array
     */
    public function getProviderData()
    {
        return $this->providerData;
    }

    /**
     * Returns extension.
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }
    
    /**
     * Set extension.
     *
     * @param string $extension
     * @return $this
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @param mixed $metadata
     * @return $this
     */
    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;
        return $this;
    }

    /**
     * Set connected user information
     *
     * @param boolean $isConnected
     * @return $this
     */
    public function setConnected($isConnected): Media
    {
        $this->connected = $isConnected;
        return $this;
    }

    /**
     * Get connected user information
     *
     * @return boolean
     */
    public function getConnected()
    {
        return $this->connected;
    }

    /**
     * @return mixed
     */
    public function getRangeIp()
    {
        return $this->rangeIp;
    }

    /**
     * @param mixed $rangeIp
     * @return $this
     */
    public function setRangeIp($rangeIp)
    {
        $this->rangeIp = $rangeIp;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param mixed $roles
     * @return $this
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUsernames()
    {
        return $this->usernames;
    }

    /**
     * @param array $usernames
     * @return $this
     */
    public function setUsernames($usernames)
    {
        $this->usernames = $usernames;
        return $this;
    }

    /**
     * Set quality
     *
     * @param integer $quality
     * @return $this
     */
    public function setQuality($quality): Media
    {
        $this->quality = null;
        if ($this->isImageable()) {
            $this->quality = $quality;
        }

        return $this;
    }

    /**
     * Get quality
     *
     * @return integer
     */
    public function getQuality()
    {
        return $this->quality;
    }
}
