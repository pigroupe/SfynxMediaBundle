<?php
namespace Sfynx\MediaBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Sfynx\MediaBundle\Layers\Domain\Entity\Media;
use Sfynx\MediaBundle\Layers\Domain\Entity\Interfaces\MediaInterface;

/**
 * Tool Filters and Functions used in twig
 *
 * @subpackage Library
 * @package    Extension
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @since      2012-01-11
 */
class PiCropExtension extends \Twig_Extension
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container The service container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     * @access public
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function getName() {
        return 'sfynx_library_crop_extension';
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * <code>
     *  {{ media_url(id, 'default_small') }}
     * </code>
     *
     * @return array An array of functions
     * @access public
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function getFunctions() {
        return [
            new \Twig_SimpleFunction('file_form', [$this, 'getFileFormFunction']),
            new \Twig_SimpleFunction('picture_form', [$this, 'getPictureFormFunction']),
            new \Twig_SimpleFunction('picture_index', [$this, 'getPictureIndexFunction']),
            new \Twig_SimpleFunction('picture_crop', [$this, 'getPictureCropFunction']),
            new \Twig_SimpleFunction('media_url', [$this, 'getMediaUrlFunction']),
        ];
    }

    /**
     * Functions
     */

    /**
     * display a file.
     *
     * <code>
     * {% if entity.media.image is defined %}
     *   {{ file_form(entity.image, "sfynx_mediabundle_mediatype_file_image_binaryContent",  'reference', 'display: block; text-align:left;')|raw }}
     * {% endif %}
     * </code>
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function getFileFormFunction(MediaInterface $media, $nameForm, $style = "display: block; text-align:center;margin: 30px auto;z-index:99999999999", $is_by_mimetype = true)
    {
        if ($media instanceof Media){
            $id = $media->getId();
            try {
                    $file_url = $this->getMediaUrlFunction($media, 'reference', false);
                    if ($is_by_mimetype){
                       $mime = str_replace('/','-',$media->getContentType());
                       $picto = '/bundles/sfynxmedia/images/icons/mimetypes/'.$mime.'.png';
                    } else {
                        $ext = substr(strtolower(strrchr(basename($file_url), ".")), 1);
                        $picto = '/bundles/sfynxmedia/images/icons/form/download-'.$ext.'.png';
                    }
                    if (!file_exists('.'.$picto)) {
                        $picto = '/bundles/sfynxmedia/images/icons/form/download-32.png';
                    }
            } catch (\Exception $e) {
                return "";
            }
            $content     = "<div id='file_$id'> \n";
            $content    .= "<a href='{$file_url}' target='_blanc' style='{$style}'> <img src='$picto' /> ".$media->getName()." <br/> {$file_url}</a>";
            $content    .= "</div> \n";
            $content    .= "<script type='text/javascript'> \n";
            $content    .= "//<![CDATA[ \n";
            $content    .= "$('#file_$id').detach().appendTo('#{$nameForm}'); \n";
            $content    .= "//]]> \n";
            $content    .= "</script> \n";

            return $content;
        }
    }

    /**
     * display a media.
     *
     * <code>
     * {% if entity.media.image is defined %}
     *   {{ picture_form(entity.media.image, "piapp_gedmobundle_blocktype_media_image_binaryContent",  'reference', 'display: block; text-align:left;')|raw }}
     * {% endif %}
     * </code>
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function getPictureFormFunction(MediaInterface $media, $nameForm, $format = 'reference', $style = "display: block; text-align:center;margin: 30px auto;", $idForm = "", $ClassName = '')
    {
        if ($media instanceof Media) {
            $id = $media->getId();
            $mediaCrop = $this->getMediaUrlFunction($media, $format, false);

            $img_balise = '<img id="'.$idForm.'" title="' . $media->getTitle() . '" src="' . $mediaCrop . '?" width="auto" height="auto" alt="' . $media->getDescriptif() . '" style="' . $style . '" >';

            $content = "<div id='picture_" . $id . "_" . $format . "' class='".$format."  ".$ClassName."' > \n";
            $content .= $img_balise;
            $content .= "</div> \n";
            $content .= "<script type='text/javascript'> \n";
            $content .= "//<![CDATA[ \n";
            $content .= "$('#{$nameForm}').before($('#picture_" . $id . "_" . $format . "')); \n";
            $content .= "//]]> \n";
            $content .= "</script> \n";

            return $content;
        }
    }

    /**
     * crop a picture.
     *
     * <code>
     * {% if entity.media.image is defined %}
     *   {{ picture_crop(entity.media.image, "default", "piapp_gedmobundle_blocktype_media_image_binaryContent")|raw}}
     *   {{ picture_crop(entity.blocgeneral.media.image, "default", "plugins_contentbundle_articletype_blocgeneral_media", '', {'unset':[0,1]})|raw}}
     * {% endif %}
     * </code>
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function getPictureCropFunction(MediaInterface $media, $format = "SfynxTemplateBundle:Template\\Crop:default.html.twig", $nameForm = "", $type = '', $options = [])
    {
        if ($format == 'default') {
            $format = "SfynxTemplateBundle:Template\\Crop:default.html.twig";
        }
        if ($media instanceof Media) {
            $crop     = $this->container->getParameter('sfynx.media.crop');
            $globals  = $this->container->get('twig')->getGlobals();
            if (!empty($type)
                && in_array($type, ['input', 'script'])
            ) {
                $templateContent = $this->container->get('twig')->loadTemplate($format);
                $crop_input = ($templateContent->hasBlock('crop_input')
                      ? $templateContent->renderBlock('crop_input', [
                          'media' => $media,
                          'nameForm' => $nameForm,
                          'crop' => $crop,
                          'options' => $options,
                          'globals' => $globals
                      ])
                      : "");
                $crop_script = ($templateContent->hasBlock('crop_script')
                      ? $templateContent->renderBlock('crop_script', [
                          'media' => $media,
                          'nameForm' => $nameForm,
                          'crop' => $crop,
                          'options' => $options,
                          'globals' => $globals
                      ])
                      : '');
                if ($type == 'input') {
                    return $crop_input;
                } elseif ($type == 'script') {
                    return $crop_script;
                }
            } else {
                $response = $this->container->get('templating')->renderResponse($format, [
                    'media' => $media,
                    'nameForm' => $nameForm,
                    'crop' => $crop,
                    'options' => $options,
                    'globals' => $globals
                ]);

                return $response->getContent();
            }
        }
    }

    /**
     * show a crop picture.
     *
     * <code>
     * {% if entity.media is defined %}
     *   {{ picture_index(entity.media, 'slider', slider_width ,  slider_height )|raw }}
     * {% endif %}
     * </code>
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function getPictureIndexFunction(MediaInterface $media, $format = '', $width='', $height='')
    {
        $id = $media->getId();
        $mediaCrop  = $this->getMediaUrlFunction($media, $format, false);

        $img_balise = '<img title="' . $media->getTitle() . '" src="' . $mediaCrop . '?' . time() . '" width="auto" height="auto" alt="' . $media->getDescriptif() . '"/>';

        $content ="<div>Dimensions de ".$format." = " .$width."x".$height."</div>";
        $content .= "<div id='picture_" . $id . $format . "' class='".$format." default_crop' > \n";
        $content .= $img_balise;
        $content .= "</div></br></br> \n";

        return $content;
    }

    /**
     * Creating a link.
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function linkFunction($label, $path, $options = [])
    {
        $attributes = '';
        foreach ( $options as $key=>$value ) {
            $attributes .= ' ' . $key . '="' . $value . '"';
        }
        return '<a href="' . $path . '"' . $attributes . '>' . $label . '</a>';
    }

    /**
     * Return the $returnTrue value if the route of the page is include in $paths value, else return the $returnFalse value.
     *
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function inPathsFunction($paths, $returnTrue = '', $returnFalse = '')
    {
        $route = (string) $this->container->get('request_stack')->getCurrentRequest()->get('_route');
        $names = explode(':', $paths);
        $is_true = false;
        if (is_array($names)) {
            foreach ($names as $k => $path) {
                if ($route == $path) {
                    $is_true = true;
                }
            }
            if ($is_true) {
                return $returnTrue;
            }
            return $returnFalse;
        } else {
            if ($route == $paths) {
                return $returnTrue;
            }
            return $returnFalse;
        }
    }

    /**
     * Return the url of a media (and put the result in cache).
     *
     * @param string $id
     * @param string $format   ["default_small", "default_big", "reference"]
     * @param string $cachable
     *
     * @return string
     * @access public
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function getMediaUrlFunction( MediaInterface $media, $format = "small", $cachable = true, $modifdate = false, $pattern = "media_")
    {
        $params = $this->container->getParameter("sfynx.media.format.{$format}");
        if (!$cachable) {
            return $media->getUrl($media->getExtension(), $params);
        }

        $id = $format . $pattern . $media->getId() . '_' . $timestamp;
        $timestamp = 0;
        if ($modifdate instanceof \Datetime) {
            $timestamp = $modifdate->getTimestamp();
        } elseif (is_string($modifdate)) {
            $timestamp = $modifdate;
        }

        $this->container
            ->get("sfynx.cache.filecache")
            ->getClient()
            ->setPath($this->container->getParameter("sfynx.media.cache_dir.media"));
        $url_public_media = $this->container
            ->get("sfynx.cache.filecache")
            ->get($id);

        if (!$url_public_media) {
            $url_public_media = $media->getUrl($media->getExtension(), $params);
            $this->container->get("sfynx.cache.filecache")->set($id, $url_public_media, 0);
        }

        return $url_public_media;
    }
}
