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
            'category' => null,
            'status' => null,
            'title' => null,
            'descriptif' => null,
            'url' => null,
            'copyright' => null,
            'position' => null,
            'publishedAt' => null,
            'noLayout' => false,
            'archived' => false,
            'enabled' => true,
            'mediadelete' => false,
            'image' => [
                'enabled' => true,
                'connected' => true,
                'descriptif' => null,
                'extension' => null,
                'metadata' => null,
                'roles' => null,
                'mimeType' => null,
                'providerStorage' => null,
                'providerName' => null,
                'providerReference' => null,
                'publicUri' => null,
                'sourceName' => null,
                'title' => null,
                'quality' => null,
//                'test' => [
//                    'enabled' => true,
//                    'connected' => true,
//                ]
            ]
        ],
        'POST' => [
            'entityId' => null,
            'category' => null,
            'status' => null,
            'title' => null,
            'descriptif' => null,
            'url' => null,
            'copyright' => null,
            'position' => null,
            'publishedAt' => null,
            'noLayout' => false,
            'archived' => false,
            'enabled' => false,
            'mediadelete' => false,
            'image' => [
                'enabled' => false,
                'connected' => false,
                'descriptif' => null,
                'extension' => null,
                'metadata' => null,
                'roles' => null,
                'mimeType' => null,
                'providerStorage' => null,
                'providerName' => null,
                'providerReference' => null,
                'publicUri' => null,
                'sourceName' => null,
                'title' => null,
                'quality' => null,
//                'test' => [
//                    'enabled' => true,
//                    'connected' => true,
//                ]
            ]
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
            'category' => ['int', 'null'],
            'noLayout' => ['bool', 'null'],
            'status' => ['string'],
            'position' => ['int', 'null'],
            'publishedAt' => ['Datetime', 'null'],
            'image' => [
                'enabled' => ['bool'],
                'connected' => ['bool'],
                'descriptif' => ['string', 'null'],
                'extension' => ['string', 'null'],
                'metadata' => ['array', 'null'],
                'roles' => ['array', 'null'],
                'mimeType' => ['string', 'null'],
                'providerStorage' => ['string', 'null'],
                'providerName' => ['string', 'null'],
                'providerReference' => ['string', 'null'],
                'publicUri' => ['string', 'null'],
                'sourceName' => ['string', 'null'],
                'title' => ['string', 'null'],
                'quality' => ['int', 'null'],
//                'test' => [
//                    'enabled' => ['bool', 'null'],
//                    'connected' => ['bool', 'null'],
//                ]
            ]
        ],
        'POST' => [
            'entityId' => ['int', 'null'],
            'category' => ['int', 'null'],
            'noLayout' => ['bool', 'null'],
            'status' => ['string'],
            'position' => ['int', 'null'],
            'publishedAt' => ['Datetime', 'null'],
            'title' => ['string'],
            'descriptif' => ['string', 'null'],
            'url' => ['string', 'null'],
            'copyright' => ['string', 'null'],
            'archived' => ['bool', 'null'],
            'mediadelete' => ['bool', 'null'],
            'enabled' => ['bool'],
            'image' => [
                'enabled' => ['bool'],
                'connected' => ['bool'],
                'descriptif' => ['string', 'null'],
                'extension' => ['string', 'null'],
                'metadata' => ['array', 'null'],
                'roles' => ['array', 'null'],
                'mimeType' => ['string', 'null'],
                'providerStorage' => ['string', 'null'],
                'providerName' => ['string', 'null'],
                'providerReference' => ['string', 'null'],
                'publicUri' => ['string', 'null'],
                'sourceName' => ['string', 'null'],
                'title' => ['string', 'null'],
                'quality' => ['int', 'null'],
//                'test' => [
//                    'enabled' => ['bool'],
//                    'connected' => ['bool'],
//                ]
            ]
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

        foreach (['noLayout', 'archived', 'enabled', 'mediadelete'] as $data) {
            if (isset($this->options[$data])) {
                $this->options[$data] = (boolean)$this->options[$data];
            }
        }
        foreach (['enabled', 'connected'] as $data) {
            if (isset($this->options['image'][$data])) {
                $this->options['image'][$data] = (boolean)$this->options['image'][$data];
            }
        }
        foreach (['category'] as $data) {
            if (isset($this->options[$data])) {
                $this->options[$data] = (int)$this->options[$data];
            }
        }
        foreach (['quality'] as $data) {
            if (isset($this->options['image'][$data])) {
                $this->options['image'][$data] = (int)$this->options['image'][$data];
            }
        }
        foreach (['metadata'] as $data) {
            if (isset($this->options['image'][$data])) {
                $this->options['image'][$data] = json_decode($this->options['image'][$data], true);
            }
        }
        $id = $this->request->get('id', '');
        $this->options['entityId'] = ('' !== $id) ? (int)$id : null;
        $this->options = (null !== $this->options) ? $this->options : [];
    }
}
