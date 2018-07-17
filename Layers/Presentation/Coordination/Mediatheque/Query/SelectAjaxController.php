<?php
namespace Sfynx\MediaBundle\Layers\Presentation\Coordination\Mediatheque\Query;

use Symfony\Component\HttpFoundation\Response;
use Sfynx\CoreBundle\Layers\Presentation\Coordination\Generalisation\AbstractSelectAjaxController;
use Sfynx\MediaBundle\Layers\Domain\Entity\Media;

/**
 * Index controller.
 *
 * @category   Sfynx\MediaBundle\Layers
 * @package    Presentation
 * @subpackage Coordination\Mediatheque\Query
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 */
class SelectAjaxController extends AbstractSelectAjaxController
{
    /** @var string */
    protected $autorization_role = 'ROLE_EDITOR';

    /**
     * get entities in ajax request for select form.
     *
     * @return Response
     * @access public
     */
    protected function init(): void
    {
        // we set param
        $this->status = $this->request->get('status', '');
        $this->setParam('pagination', $this->request->get('pagination', null));
        $this->setParam('max', $this->request->get('max', 10));
        $this->setParam('keyword', [
            0 => [
                'field_name' => 'title',
                'field_value' => $this->request->get('keyword', ''),
                'field_trans' => true,
                'field_trans_name' => 'trans',
            ],
        ]);
//        $this->setParam('cacheQuery_hash', [
//            'time'      => 3600,
//            'namespace' => 'hash_list_auth_user'
//        ]);
        $this->setParam('query', $this->manager->getQueryRepository()->createQueryBuilder('a')
            ->leftJoin('a.translations', 'trans')
            ->leftJoin('a.image', 'm')
//            ->andWhere('a.image IS NOT NULL')
//            ->andWhere("a.status = '{$this->status}'")
        );
    }

    /**
     * Select all entities.
     *
     * @param array  $entities
     * @param string $locale
     *
     * @return Response
     * @access public
     */
    protected function renderQuery($entities, $locale)
    {
        $tab = [];
        foreach ($entities as $obj) {
            $content = $obj->getId();
            $title   = $obj->getTitle();
            $cat     = $obj->getCategory();
            if ($title) {
                $content .=  " - " .$title;
            }
            if (!(null === $cat)) {
                $content .=  ' ('. $cat->getName() .')';
            }
            if (($this->status == 'image')
                && ($obj->getImage() instanceof Media)
            ) {
                $content .= "<img width='40px' src='".$obj->getImage()->getUrl(null, [
                    'maxAge' => 31536000,
                    'resize' => 1,
                    'width' => 100,
                ])."' alt='Photo'/>";
            }
            $tab[] = [
                'id'   => $obj->getId(),
                'text' => $this->twig->render($content)
            ];
        }

        return $tab;
    }
}