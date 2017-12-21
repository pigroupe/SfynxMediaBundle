<?php
namespace Sfynx\MediaBundle\Layers\Presentation\Coordination\Mediatheque\Command;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sfynx\CoreBundle\Controller\abstractController;
use Sfynx\CoreBundle\Layers\Infrastructure\Exception\ControllerException;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Sfynx\MediaBundle\Layers\Domain\Entity\Mediatheque;
use Sfynx\MediaBundle\Layers\Domain\Entity\Media;
use Sfynx\MediaBundle\Layers\Application\Validation\Type\MediathequeType;
use Sfynx\MediaBundle\Layers\Domain\Entity\Translation\MediaTranslation;

/**
 * Media controller.
 *
 *
 * @category   PI_CRUD_Controllers
 * @package    Controller
 *
 * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
 */
class MediathequeController extends abstractController
{
    protected $_entityName = "SfynxMediaBundle:Mediatheque";

    /**
     * Enabled Media entities.
     *
     * @Route("/content/gedmo/media/enabled", name="sfynx_media_mediatheque_enabledentity_ajax")
     * @Secure(roles="ROLE_EDITOR")
     * @return Response
     * @access public
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function enabledajaxAction()
    {
        return parent::enabledajaxAction();
    }

    /**
     * Disable Media entities.
     *
     * @Route("/content/gedmo/media/disable", name="sfynx_media_mediatheque_disablentity_ajax")
     * @Secure(roles="ROLE_EDITOR")
     * @return Response
     * @access public
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function disableajaxAction()
    {
        return parent::disableajaxAction();
    }

    /**
     * Position Media entities.
     *
     * @Route("/content/gedmo/media/position", name="sfynx_media_mediatheque_position_ajax")
     * @Secure(roles="ROLE_EDITOR")
     * @return Response
     * @access public
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function positionajaxAction()
    {
        return parent::positionajaxAction();
    }

    /**
     * Delete Media entities.
     *
     * @Route("/content/gedmo/media/delete", name="sfynx_media_mediatheque_deletentity_ajax")
     * @Secure(roles="ROLE_EDITOR")
     * @return Response
     * @access public
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function deleteajaxAction()
    {
        return parent::deletajaxAction();
    }

    /**
     * Archive a Media entity.
     *
     * @Route("/content/gedmo/media/archive", name="sfynx_media_mediatheque_archiveentity_ajax")
     * @Secure(roles="ROLE_EDITOR")
     * @return Response
     * @access public
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function archiveajaxAction()
    {
        return parent::archiveajaxAction();
    }

    /**
     * get entities in ajax request for select form.
     *
     * @Route("/content/gedmo/media/select/{type}", name="sfynx_media_mediatheque_selectentity_ajax")
     * @Secure(roles="ROLE_EDITOR")
     * @return Response
     * @access public
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function selectajaxAction($type)
    {
    	$request = $this->container->get('request_stack')->getCurrentRequest();
    	$em      = $this->getDoctrine()->getManager();
    	$locale  = $this->container->get('request_stack')->getCurrentRequest()->getLocale();
    	//
    	$pagination = $this->container->get('request_stack')->getCurrentRequest()->get('pagination', null);
    	$keyword    = $this->container->get('request_stack')->getCurrentRequest()->get('keyword', '');
    	$MaxResults = $this->container->get('request_stack')->getCurrentRequest()->get('max', 10);
    	// we set query
        $query  = $em->getRepository("SfynxMediaBundle:Mediatheque")->getAllByCategory('', null, '', '', false);
        $query
        ->leftJoin('a.translations', 'trans')
        ->leftJoin('a.image', 'm')
        ->andWhere('a.image IS NOT NULL')
        ->andWhere("a.status = '{$type}'");
        //
        $keyword = array(
            0 => array(
                'field_name' => 'title',
                'field_value' => $keyword,
                'field_trans' => true,
                'field_trans_name' => 'trans',
            ),
        );
        // we set type value
        $this->type = $type;

        return $this->selectajaxQuery($pagination, $MaxResults, $keyword, $query, $locale, true, array(
            'time'      => 3600,
            'namespace' => 'hash_list_gedmomedia'
        ));
    }

    /**
     * Select all entities.
     *
     * @return Response
     * @access protected
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    protected function renderselectajaxQuery($entities, $locale)
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
            if (($this->type == 'image')
                    && ($obj->getImage() instanceof Media)
            ) {
                $content .= "<img width='100px' src=\"{{ media_url('".$obj->getImage()->getId()."', 'small', true, '".$obj->getUpdatedAt()->format('Y-m-d H:i:s')."', 'gedmo_media_') }}\" alt='Photo'/>";
            }
            $tab[] = array(
                'id'   => $obj->getId(),
                'text' => $this->container->get('twig')->render($content, [])
            );
        }

    	return $tab;
    }

    /**
     * Lists all Media entities.
     *
     * @Secure(roles="IS_AUTHENTICATED_ANONYMOUSLY")
     * @return Response
     * @access public
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function indexAction()
    {
        $em      = $this->getDoctrine()->getManager();
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $locale  = $request->getLocale();
        // we get params
        $NoLayout   = $request->query->get('NoLayout');
        $category   = $request->query->get('category');
        if (is_array($category) && isset($category['__isInitialized__'])) {
            $category = $category['__isInitialized__'];
        }
        // weg set query
        $query = $em->getRepository("SfynxMediaBundle:Mediatheque")->getAllByCategory($category, null, '', '', false);
        $query
        ->leftJoin('a.image', 'm')
        ->leftJoin('a.category', 'c')
        ->andWhere('a.image IS NOT NULL');

        //
        $is_Server_side = true;
        //
        if ($request->isXmlHttpRequest() && $is_Server_side) {
           $aColumns    = array('a.id','c.name','a.status','a.title',"a.enabled",'a.created_at', 'a.updated_at',"a.enabled","a.enabled");
           $q1 = clone $query;
           $q2 = clone $query;
           $result    = $this->createAjaxQuery('select',$aColumns, $q1, 'a', null, array(
                    0 =>array('column'=>'a.created_at', 'format'=>'Y-m-d', 'idMin'=>'minc', 'idMax'=>'maxc'),
                    1 =>array('column'=>'a.updated_at', 'format'=>'Y-m-d', 'idMin'=>'minu', 'idMax'=>'maxu')
                ), array(
                      'time'      => 3600,
                      'namespace' => 'hash_list_gedmomedia'
                )
           );
           $total    = $this->createAjaxQuery('count',$aColumns, $q2, 'a', null, array(
                    0 =>array('column'=>'a.created_at', 'format'=>'Y-m-d', 'idMin'=>'minc', 'idMax'=>'maxc'),
                    1 =>array('column'=>'a.updated_at', 'format'=>'Y-m-d', 'idMin'=>'minu', 'idMax'=>'maxu')
                ), array(
                      'time'      => 3600,
                      'namespace' => 'hash_list_gedmomedia'
                )
           );

           $output = array(
                "sEcho" => intval($request->get('sEcho')),
                "iTotalRecords" => $total,
                "iTotalDisplayRecords" => $total,
                "aaData" => array()
           );

           foreach ($result as $e) {
              $row = array();
              $row[] = $e->getId() . '_row_' . $e->getId();
              $row[] = $e->getId();

              if (is_object($e->getCategory())) {
                  $row[] = (string) $e->getCategory()->getName();
              } else {
                  $row[] = "";
              }

              $row[] = (string) $e->getStatus() . '('.$e->getId().')';

              if (is_object($e->getImage())) {
            	  $title = $e->getTitle();
              	  if (!empty($title)) {
              			$row[] = (string) $title . '('. $e->getImage()->getName() . ')';
              	  } else {
              			$row[] = (string) $e->getImage()->getName();
              	  }
                  $url = $this->container->get('sfynx.media.twig.extension.crop')->getMediaUrlFunction($e->getImage(), 'reference', true, $e->getUpdatedAt(), 'media_');
              } else {
                  $row[] = "";
                  $url = "#";
              }

              if ($e->getStatus() == 'image') {
              	$UrlPicture = $this->container->get('sfynx.media.twig.extension.crop')->getMediaUrlFunction($e->getImage(), 'reference', true, $e->getUpdatedAt(), 'gedmo_media_');
              	$row[] = (string) '<a href="#" title=\'<img src="'.$UrlPicture.'" class="info-tooltip-image" >\' class="info-tooltip"><img width="20px" src="'.$UrlPicture.'"></a>';
              } else {
              	$row[] = "";
              }

              if (is_object($e->getCreatedAt())) {
              	$row[] = (string) $e->getCreatedAt()->format('Y-m-d');
              } else {
              	$row[] = "";
              }

              if (is_object($e->getUpdatedAt())) {
                  $row[] = (string) $e->getUpdatedAt()->format('Y-m-d');
              } else {
                  $row[] = "";
              }
              // create enabled/disabled buttons
              $Urlenabled     = $this->container->get('templating.helper.assets')->getUrl($this->container->getParameter('sfynx.template.theme.layout.admin.grid.img')."enabled.png");
              $Urldisabled     = $this->container->get('templating.helper.assets')->getUrl($this->container->getParameter('sfynx.template.theme.layout.admin.grid.img')."disabled.png");
              if ($e->getEnabled()) {
                  $row[] = (string) '<img width="17px" src="'.$Urlenabled.'">';
              } else {
                  $row[] = (string) '<img width="17px" src="'.$Urldisabled.'">';
              }
              // create action links
              $route_path_show = $this->container->get('sfynx.tool.twig.extension.route')->getUrlByRouteFunction('sfynx_media_mediatheque_show', array('id'=>$e->getId(), 'NoLayout'=>$NoLayout, 'category'=>$category, 'status'=>$e->getStatus()));
              $route_path_edit = $this->container->get('sfynx.tool.twig.extension.route')->getUrlByRouteFunction('sfynx_media_mediatheque_edit', array('id'=>$e->getId(), 'NoLayout'=>$NoLayout, 'category'=>$category, 'status'=>$e->getStatus()));
              if (is_object($e->getImage()) && ($e->getStatus() == 'image')) {
                  $actions = '<a href="'.$route_path_show.'" title="'.$this->container->get('translator')->trans('pi.grid.action.show').'" data-ui-icon="ui-icon-show" class="button-ui-icon-show info-tooltip" >'.$this->container->get('translator')->trans('pi.grid.action.show').'</a>'; //actions
              } else {
                  $actions = '<a href="'.$url.'" target="_blank" title="'.$this->container->get('translator')->trans('pi.grid.action.show').'" data-ui-icon="ui-icon-show" class="button-ui-icon-show info-tooltip" >'.$this->container->get('translator')->trans('pi.grid.action.show').'</a>'; //actions
              }
              $actions .= '<a href="'.$route_path_edit.'" title="'.$this->container->get('translator')->trans('pi.grid.action.edit').'" data-ui-icon="ui-icon-edit" class="button-ui-icon-edit info-tooltip" >'.$this->container->get('translator')->trans('pi.grid.action.edit').'</a>'; //actions
              $row[] = (string) $actions;

              $output['aaData'][] = $row ;
            }
            $response = new Response(json_encode( $output ));
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }
        if (!$is_Server_side) {
            if ($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            } else {
                $query = $em->getRepository("SfynxMediaBundle:Mediatheque")->setContainer($this->container)->checkRoles($query);
            }
            $query     = $em->getRepository("SfynxMediaBundle:Mediatheque")->cacheQuery($query->getQuery(), 3600, 3 /*\Doctrine\ORM\Cache::MODE_NORMAL */, true, 'hash_list_gedmomedia');
            $entities  = $em->getRepository("SfynxMediaBundle:Mediatheque")->findTranslationsByQuery($locale, $query, 'object', false);
        } else {
            $entities  = null;
        }

        return $this->render("SfynxMediaBundle:Mediatheque:index.html.twig", array(
            'isServerSide' => $is_Server_side,
            'entities' => $entities,
            'NoLayout' => $NoLayout,
            'category' => $category,
        ));
    }

    /**
     * Finds and displays a Media entity.
     *
     * @Secure(roles="IS_AUTHENTICATED_ANONYMOUSLY")
     * @return Response
     * @access public
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function showAction($id)
    {
        $em     = $this->getDoctrine()->getManager();
        $locale = $this->container->get('request_stack')->getCurrentRequest()->getLocale();
        $entity = $em->getRepository("SfynxMediaBundle:Mediatheque")->findOneByEntity($locale, $id, 'object');

        $NoLayout   = $this->container->get('request_stack')->getCurrentRequest()->query->get('NoLayout');
        if (!$NoLayout)     $template = "show.html.twig"; else $template = "show.html.twig";

        $category   = $this->container->get('request_stack')->getCurrentRequest()->query->get('category');
        if (is_array($category) && isset($category['__isInitialized__']))
            $category = $category['__isInitialized__'];

        if (!$entity) {
            throw ControllerException::NotFoundEntity('Media');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render("SfynxMediaBundle:Mediatheque:$template", array(
            'entity'      => $entity,
            'NoLayout'    => $NoLayout,
            'category'    => $category,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to create a new Media entity.
     *
     * @Secure(roles="ROLE_EDITOR")
     * @return Response
     * @access public
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function newAction()
    {
        $em     = $this->getDoctrine()->getManager();
        $locale = $this->container->get('request_stack')->getCurrentRequest()->getLocale();
        $status = $this->container->get('request_stack')->getCurrentRequest()->query->get('status');
        $entity = new Mediatheque();
        $entity->setStatus($status);
        $entity->setUpdatedAt(new \Datetime());
        //$form   = $this->createForm(new MediaType($this->container, $em, $status), $entity, array('show_legend' => false));
        $form   = $this->createForm('sfynx_mediabundle_mediatype_' . $status, $entity, array('show_legend' => false));

        $NoLayout   = $this->container->get('request_stack')->getCurrentRequest()->query->get('NoLayout');
        if (!$NoLayout)    $template = "new.html.twig";  else     $template = "new.html.twig";

        $category   = $this->container->get('request_stack')->getCurrentRequest()->query->get('category');
        if (is_array($category) && isset($category['__isInitialized__']))
            $category = $category['__isInitialized__'];

        return $this->render("SfynxMediaBundle:Mediatheque:$template", array(
                'entity' => $entity,
                'form'   => $form->createView(),
                'NoLayout'  => $NoLayout,
                'category'      => $category,
                'status'    => $status,
        ));
    }

    /**
     * Creates a new Media entity.
     *
     * @Secure(roles="ROLE_EDITOR")
     * @return Response
     * @access public
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function createAction()
    {
        $em     = $this->getDoctrine()->getManager();
        $locale    = $this->container->get('request_stack')->getCurrentRequest()->getLocale();
        $status = $this->container->get('request_stack')->getCurrentRequest()->query->get('status');
        $NoLayout   = $this->container->get('request_stack')->getCurrentRequest()->query->get('NoLayout');
        $category   = $this->container->get('request_stack')->getCurrentRequest()->query->get('category');
        if (is_array($category) && isset($category['__isInitialized__'])) {
            $category = $category['__isInitialized__'];
        }
        $entity  = new Mediatheque();
        $entity->setStatus($status);
        $request = $this->getRequest();
        $form    = $this->createForm('sfynx_mediabundle_mediatype_' . $status, $entity, array('show_legend' => false));
        $form->bind($request);
        if ($form->isValid()) {
            $entity->setTranslatableLocale($locale);
            $em->persist($entity);
            $em->flush();
            // to delete cache list query
            $this->deleteAllCacheQuery('hash_list_gedmomedia');

            return $this->redirect($this->generateUrl('sfynx_media_mediatheque_show', array('id' => $entity->getId(), 'NoLayout' => $NoLayout, 'category' => $category)));
        }

        return $this->render("SfynxMediaBundle:Mediatheque:new.html.twig", array(
                'entity'     => $entity,
                'form'       => $form->createView(),
                'NoLayout'  => $NoLayout,
                'category'      => $category,
                'status'    => $status,
        ));
    }

    /**
     * Displays a form to edit an existing Media entity.
     *
     * @Secure(roles="ROLE_EDITOR")
     * @return Response
     * @access public
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function editAction($id)
    {
        $em     = $this->getDoctrine()->getManager();
        $locale    = $this->container->get('request_stack')->getCurrentRequest()->getLocale();
        $entity = $em->getRepository("SfynxMediaBundle:Mediatheque")->findOneByEntity($locale, $id, 'object');

        $status        = $this->container->get('request_stack')->getCurrentRequest()->query->get('status');
        $NoLayout   = $this->container->get('request_stack')->getCurrentRequest()->query->get('NoLayout');
        if (!$NoLayout)    $template = "edit.html.twig";  else    $template = "edit.html.twig";

        $category   = $this->container->get('request_stack')->getCurrentRequest()->query->get('category');
        if (is_array($category) && isset($category['__isInitialized__']))
            $category = $category['__isInitialized__'];

        if (!$entity) {
            $entity = $em->getRepository("SfynxMediaBundle:Mediatheque")->find($id);
            $entity->addTranslation(new MediaTranslation($locale));
        }

        $entity->setUpdatedAt(new \Datetime());
        $editForm   = $this->createForm('sfynx_mediabundle_mediatype_' . $status, $entity, array('show_legend' => false));
        $deleteForm = $this->createDeleteForm($id);

        return $this->render("SfynxMediaBundle:Mediatheque:$template", array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'NoLayout'       => $NoLayout,
            'category'      => $category,
            'status'      => $status,
        ));
    }

    /**
     * Edits an existing Media entity.
     *
     * @Secure(roles="ROLE_EDITOR")
     * @return Response
     * @access public
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function updateAction($id)
    {
        $em     = $this->getDoctrine()->getManager();
        $locale = $this->container->get('request_stack')->getCurrentRequest()->getLocale();
        $entity = $em->getRepository("SfynxMediaBundle:Mediatheque")->findOneByEntity($locale, $id, "object");

        $status        = $this->container->get('request_stack')->getCurrentRequest()->query->get('status');
        $NoLayout   = $this->container->get('request_stack')->getCurrentRequest()->query->get('NoLayout');
        if (!$NoLayout)    $template = "edit.html.twig";  else    $template = "edit.html.twig";

        $category   = $this->container->get('request_stack')->getCurrentRequest()->query->get('category');
        if (is_array($category) && isset($category['__isInitialized__']))
            $category = $category['__isInitialized__'];

        if (!$entity) {
            $entity = $em->getRepository("SfynxMediaBundle:Mediatheque")->find($id);
        }

        $editForm   = $this->createForm('sfynx_mediabundle_mediatype_' . $status, $entity, array('show_legend' => false));
        $deleteForm = $this->createDeleteForm($id);

        $editForm->bind($this->getRequest(), $entity);
        if ($editForm->isValid()) {
            $entity->setTranslatableLocale($locale);
            $em->persist($entity);
            $em->flush();
            // to delete cache list query
            $this->deleteAllCacheQuery('hash_list_gedmomedia');

            return $this->redirect($this->generateUrl('sfynx_media_mediatheque_edit', array('id' => $id, 'NoLayout' => $NoLayout, 'category' => $category, 'status' => $status)));
        }

        return $this->render("SfynxMediaBundle:Mediatheque:$template", array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'NoLayout'       => $NoLayout,
            'status'    => $status,
            'category'      => $category,
        ));
    }

    /**
     * Deletes a Media entity.
     *
     * @Secure(roles="ROLE_EDITOR")
     * @return RedirectResponse
     * @access public
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function deleteAction($id)
    {
        $em      = $this->getDoctrine()->getManager();
        $locale     = $this->container->get('request_stack')->getCurrentRequest()->getLocale();

        $NoLayout   = $this->container->get('request_stack')->getCurrentRequest()->query->get('NoLayout');
        $category   = $this->container->get('request_stack')->getCurrentRequest()->query->get('category');

        $form      = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bind($request);

        if ($form->isValid()) {
            $entity = $em->getRepository("SfynxMediaBundle:Mediatheque")->findOneByEntity($locale, $id, 'object');
            if (!$entity) {
                throw ControllerException::NotFoundEntity('Media');
            }
            try {
                $em->remove($entity);
                $em->flush();
                // to delete cache list query
                $this->deleteAllCacheQuery('hash_list_gedmomedia');
            } catch (\Exception $e) {
                $this->container->get('request_stack')->getCurrentRequest()->getSession()->getFlashBag()->clear();
                $this->container->get('request_stack')->getCurrentRequest()->getSession()->getFlashBag()->add('notice', 'pi.session.flash.wrong.undelete');
            }
        }

        return $this->redirect($this->generateUrl('sfynx_media_mediatheque', array('NoLayout' => $NoLayout, 'category' => $category)));
    }

    protected function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

    /**
     * Template : Finds and displays a Media entity.
     *
     * @Cache(maxage="86400")
     * @return Response
     * @access public
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function _template_showAction($id, $template = '_tmp_show.html.twig', $lang = "")
    {
        $em     = $this->getDoctrine()->getManager();

        if (empty($lang))
            $lang    = $this->container->get('request_stack')->getCurrentRequest()->getLocale();

        $entity = $em->getRepository("SfynxMediaBundle:Mediatheque")->findOneByEntity($lang, $id, 'object', false);

        if (!$entity) {
            throw ControllerException::NotFoundEntity('Media');
        }

        return $this->render("SfynxMediaBundle:Mediatheque:$template", array(
                'entity'      => $entity,
                'locale'   => $lang,
                'lang'       => $lang,
        ));
    }

    /**
     * Template : Finds and displays a list of Media entity.
     *
     * @Cache(maxage="86400")
     * @return Response
     * @access public
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function _template_listAction($category = '', $MaxResults = null, $template = '_tmp_list.html.twig', $order = 'DESC', $lang = "")
    {
        $em         = $this->getDoctrine()->getManager();

        if (empty($lang))
            $lang   = $this->container->get('request_stack')->getCurrentRequest()->getLocale();

        $query      = $em->getRepository("SfynxMediaBundle:Mediatheque")->getAllByCategory($category, $MaxResults, '', $order)->getQuery();
        $entities   = $em->getRepository("SfynxMediaBundle:Mediatheque")->findTranslationsByQuery($lang, $query, 'object', false);

        return $this->render("SfynxMediaBundle:Mediatheque:$template", array(
            'entities' => $entities,
            'cat'       => ucfirst($category),
            'locale'   => $lang,
            'lang'       => $lang,
        ));
    }
}
