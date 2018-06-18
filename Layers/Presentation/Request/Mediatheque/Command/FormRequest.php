<?php
namespace Sfynx\MediaBundle\Layers\Presentation\Request\Mediatheque\Command;

use Symfony\Component\OptionsResolver\Options;

use Sfynx\CoreBundle\Layers\Presentation\Request\Generalisation\AbstractFormRequest;

/**
 * Class FormRequest
 *
 * @category Sfynx\MediaBundle\Layers
 * @package Presentation
 * @subpackage Request\Mediatheque\Command
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 */
class FormRequest extends AbstractFormRequest
{
    /**
     * @var array $defaults List of default values for optional parameters.
     */
    protected $defaults = [
        'GET' => [
            'entityId' => null,
            'category' => '',
            'noLayout' => '',
            'status' => null,
            'title' => null,
            'descriptif' => null,
            'url' => null,
            'copyright' => null,
            'position' => null,
            'publishedAt' => null,
            'archived' => false,
            'enabled' => true,
            'mediadelete' => false,
            'image' => null
        ],
        'POST' => [
            'entityId' => null,
            'category' => '',
            'noLayout' => '',
            'status' => null,
            'title' => null,
            'descriptif' => null,
            'url' => null,
            'copyright' => null,
            'position' => null,
            'publishedAt' => null,
            'archived' => false,
            'enabled' => false,
            'mediadelete' => false,
            'image' => null
        ]
    ];

    /**
     * @var string[] $required List of required parameters for each methods.
     */
    protected $required = [
        'GET'  => ['status'],
        'POST' => ['entityId', 'status', 'title', 'enabled'],
        'PATCH' => 'POST'
    ];

    /**
     * @var array[] $allowedTypes List of allowed types for each methods.
     */
    protected $allowedTypes = [
        'GET' => [
            'category' => ['string', 'null'],
            'noLayout' => ['bool', 'null'],
            'status' => ['string'],
        ],
        'POST' => [
            'entityId' => ['int', 'null'],
            'category' => ['string', 'null'],
            'noLayout' => ['bool', 'null'],
            'status' => ['string'],
            'title' => ['string'],
            'descriptif' => ['string', 'null'],
            'url' => ['string', 'null'],
            'mediadelete' => ['bool', 'null'],
            'copyright' => ['string', 'null'],
            'position' => ['int', 'null'],
            'publishedAt' => ['string', 'null'],
            'archived' => ['bool', 'null'],
            'enabled' => ['bool'],
            'image' => ['array', 'null']
        ],
        'PATCH' => 'POST'
    ];

    /**
     * @return void
     */
    protected function setOptions()
    {
        $this->status = $this->request->get('status');
        $this->options = $this->request->getRequest()->get('sfynx_mediabundle_mediatype_' . $this->status);
        $this->options['status'] = $this->status;
        $this->options['noLayout'] = $this->request->getQuery()->get('NoLayout');

        foreach (['noLayout','archived', 'enabled', 'mediadelete'] as $data) {
            if (isset($this->options[$data])) {
                $this->options[$data] = (int)$this->options[$data] ? true : false;
            }
        }
        $id = $this->request->get('id', '');
        $this->options['entityId'] = ('' !== $id) ? (int)$id : null;
        $this->options = (null !== $this->options) ? $this->options : [];
    }
}
