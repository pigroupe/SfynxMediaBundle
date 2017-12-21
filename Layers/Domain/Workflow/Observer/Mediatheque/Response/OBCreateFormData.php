<?php
namespace Sfynx\MediaBundle\Layers\Domain\Workflow\Observer\Mediatheque\Response;

use stdClass;
use Sfynx\CoreBundle\Layers\Domain\Service\Manager\Generalisation\Interfaces\ManagerInterface;
use Sfynx\CoreBundle\Layers\Domain\Service\Request\Generalisation\RequestInterface;
use Sfynx\CoreBundle\Layers\Domain\Workflow\Observer\Generalisation\Response\AbstractCreateFormData;
use Sfynx\CoreBundle\Layers\Infrastructure\Exception\WorkflowException;

use PiApp\GedmoBundle\Layers\Domain\Entity\Category;

/**
 * Class OBCreateFormData
 *
 * @category Sfynx\MediaBundle\Layers
 * @package Domain
 * @subpackage Workflow\Observer\Mediatheque\Response
 * @author     Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @copyright  2016 PI-GROUPE
 */
class OBCreateFormData extends AbstractCreateFormData
{
    /** @var ManagerInterface */
    protected $managerGedmoCategory;

    /**
     * OBCreateFormData constructor.
     *
     * @param RequestInterface $request
     * @param ManagerInterface $managerGedmoCategory
     */
    public function __construct(RequestInterface $request, ManagerInterface $managerGedmoCategory)
    {
        parent::__construct($request);
        $this->managerGedmoCategory = $managerGedmoCategory;
    }

    /**
     * {@inheritdoc}
     */
    protected function process(): bool
    {
        try {
            if ($this->wfHandler->entity->getCategory()) {
                $id_category = $this->wfHandler->entity->getCategory()->getId();
            } else {
                $id_category = $this->wfHandler->command->category;
            }
            if (isset($_POST['sfynx_mediabundle_mediatype_image']['category'])) {
                $id_category = $_POST['sfynx_mediabundle_mediatype_image']['category'];
            }
            $this->wfLastData->formViewData['id_category'] = $id_category;
            $this->wfLastData->formViewData['status'] = $this->wfHandler->command->status;

            $this->wfLastData->formViewData['categories'] = $this->managerGedmoCategory
                ->getQueryRepository()
                ->getAllByType(Category::TYPE_MEDIA, $id_category)
                ;

//            // Register data values
//            $this->wfLastData->formViewData['categories'] = $this->managerGedmoCategory
//                ->getQueryRepository()
//                ->Result($query)
//                ->getResults(\Doctrine\ORM\AbstractQuery::HYDRATE_OBJECT);

        } catch (Exception $e) {
            throw WorkflowException::noCreatedFormData();
        }
        return true;
    }
}
