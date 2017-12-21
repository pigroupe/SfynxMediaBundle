<?php
namespace Sfynx\MediaBundle\Layers\Domain\Workflow\Observer\Mediatheque\Response;

use Exception;
use stdClass;
use Sfynx\CoreBundle\Layers\Domain\Workflow\Observer\Generalisation\Response\AbstractCreateIndexBodyJson;
use Sfynx\CoreBundle\Layers\Infrastructure\Exception\WorkflowException;

/**
 * Class OBCreateIndexBodyJson
 *
 * @category Sfynx\MediaBundle\Layers
 * @package Domain
 * @subpackage Workflow\Observer\Mediatheque\Response
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2016 PI-GROUPE
 */
class OBCreateIndexBodyJson extends AbstractCreateIndexBodyJson
{
    /**
     * {@inheritdoc}
     */
    protected function process(): bool
    {
        $NoLayout = $this->wfHandler->query->NoLayout;
        $category = $this->wfHandler->query->category;

        $this->wfLastData->rows = [];
        try {
            foreach ($this->wfHandler->result as $entity) {
                $row = [];
                $row[] = $entity->getId() . '_row_' . $entity->getId();
                $row[] = $entity->getId();

                if (is_object($entity->getCategory())) {
                    $row[] = (string) $entity->getCategory()->getName();
                } else {
                    $row[] = '';
                }

                $row[] = (string) $entity->getStatus() . '('.$entity->getId().')';

                if (is_object($entity->getImage())) {
                    $title = $entity->getTitle();
                    if (!empty($title)) {
                        $row[] = (string) $title . '('. $entity->getImage()->getName() . ')';
                    } else {
                        $row[] = (string) $entity->getImage()->getName();
                    }
                } else {
                    $row[] = '';
                }

                $url1 = $entity->getImage()->getUrl('png', ['resize' => 1, 'height' => 300]);
                $url2 = $entity->getImage()->getUrl($entity->getImage()->getExtension());
                if (is_object($entity->getImage()) && $entity->getImage()->isImageable()) {
                    $row[] = (string) '<a href="#" title=\'<img src="'.$url1.'" class="info-tooltip-image" >\' class="info-tooltip"><img width="20px" src="'.$url1.'"></a>';
                } else {
                    $row[] = (string) '<a href="#" title=\'<img src="'.$url2.'" class="info-tooltip-image" >\' class="info-tooltip"><img width="20px" src="'.$url2.'"></a>';
                }

                if (is_object($entity->getCreatedAt())) {
                    $row[] = (string) $entity->getCreatedAt()->format('Y-m-d');
                } else {
                    $row[] = '';
                }

                if (is_object($entity->getUpdatedAt())) {
                    $row[] = (string) $entity->getUpdatedAt()->format('Y-m-d');
                } else {
                    $row[] = '';
                }
                // create enabled/disabled buttons
                $Urlenabled  = $this->param->sfynx_template_theme_layout_admin_grid_img . 'enabled.png';
                $Urldisabled = $this->param->sfynx_template_theme_layout_admin_grid_img . 'disabled.png';
                if ($entity->getEnabled()) {
                    $row[] = (string) '<img width="17px" src="'.$Urlenabled.'">';
                } else {
                    $row[] = (string) '<img width="17px" src="'.$Urldisabled.'">';
                }
                // create action links
                $route_path_show = $this->generateUrl('sfynx_media_mediatheque_show', array('id'=>$entity->getId(), 'NoLayout'=>$NoLayout, 'category'=>$category, 'status'=>$entity->getStatus()));
                $route_path_edit = $this->generateUrl('sfynx_media_mediatheque_edit', array('id'=>$entity->getId(), 'NoLayout'=>$NoLayout, 'category'=>$category, 'status'=>$entity->getStatus()));
                if (is_object($entity->getImage()) && $entity->getImage()->isImageable(false)) {
                    $actions = '<a href="'.$route_path_show.'" title="'.$this->translator->trans('pi.grid.action.show').'" data-ui-icon="ui-icon-show" class="button-ui-icon-show info-tooltip" >'.$this->translator->trans('pi.grid.action.show').'</a>'; //actions
                } else {
                    $actions = '<a href="'.$url2.'" target="_blank" title="'.$this->translator->trans('pi.grid.action.show').'" data-ui-icon="ui-icon-show" class="button-ui-icon-show info-tooltip" >'.$this->translator->trans('pi.grid.action.show').'</a>'; //actions
                }
                $actions .= '<a href="'.$route_path_edit.'" title="'.$this->translator->trans('pi.grid.action.edit').'" data-ui-icon="ui-icon-edit" class="button-ui-icon-edit info-tooltip" >'.$this->translator->trans('pi.grid.action.edit').'</a>'; //actions
                $row[] = (string) $actions;

                $output['aaData'][] = $row ;

                $this->wfLastData->rows[] = $row ;
            }
        } catch (Exception $e) {
            throw WorkflowException::noCreatedViewForm();
        }
        return true;
    }
}
