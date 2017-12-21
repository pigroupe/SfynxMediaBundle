<?php
namespace Sfynx\MediaBundle\Layers\Domain\Entity\Interfaces;

use Sfynx\CoreBundle\Layers\Domain\Model\Interfaces\EntityInterface;

/**
 * Interface EntityInterface
 *
 * @category Sfynx\MediaBundle\Layer
 * @package Domain
 * @subpackage Entity\Interfaces
 */
interface MediaInterface extends EntityInterface
{
    /**
     * Get title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Get descriptif
     *
     * @return text
     */
    public function getDescriptif ();

    /**
     * Returns url.
     *
     * @param string $extension
     * @return string
     */
    public function getUrl($extension = null, $query = []);

    /**
     * isImageable
     */
    public function isImageable($withPdf = true);
}
