<?php
namespace Sfynx\MediaBundle\Layers\Application\Cqrs\Mediatheque\Command;

use Sfynx\CoreBundle\Layers\Application\Command\Generalisation\AbstractCommand;

/**
 * Class FormCommand.
 *
 * @category   Sfynx\MediaBundle\Layers
 * @package    Application
 * @subpackage Cqrs\Mediatheque\Command
 */
class FormCommand extends AbstractCommand
{
    /** @var int */
    public $entityId;
    /** @var string */
    public $category;
    /** @var string */
    public $noLayout;
    /** @var string */
    public $status;
    /** @var string */
    public $title;
    /** @var string */
    public $descriptif;
    /** @var string */
    public $url;
    /** @var string */
    public $image;
    /** @var string */
    public $image2;
    /** @var string */
    public $mediadelete;
    /** @var string */
    public $copyright;
    /** @var string */
    public $position;
    /** @var string */
    public $createdAt;
    /** @var string */
    public $updatedAt;
    /** @var string */
    public $publishedAt;
    /** @var string */
    public $archiveAt;
    /** @var bool */
    public $archived;
    /** @var  bool */
    public $enabled;
}
