<?php
/**
 * This file is part of the <Auth> project.
 *
 * @subpackage   User
 * @package    Builder
 * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
 * @since 2012-01-02
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sfynx\MediaBundle\Layers\Domain\Repository\Query;

/**
 * MediathequeQueryRepositoryInterface
 *
 * @category Sfynx\MediaBundle\Layers
 * @package Domain
 * @subpackage Repository\Command
 *
 * @author SFYNX <sfynx@pi-groupe.net>
 * @link http://www.sfynx.fr
 * @license LGPL (https://opensource.org/licenses/LGPL-3.0)
 */
interface MediathequeQueryRepositoryInterface
{
    /**
     * Gets all entities by one category.
     *
     * @param string  $category
     * @param integer $MaxResults
     * @param string  $ORDER_PublishDate ['ASC', 'DESC']
     * @param string  $ORDER_Position    ['ASC', 'DESC']
     * @param boolean $enabled
     *
     * @return array\entity
     * @access public
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     * @since  2012-03-15
     */
    public function getAllByParams($category = '', $MaxResults = null, $ORDER_PublishDate = '', $ORDER_Position = '', $enabled = true);

    /**
     * Gets all entities by one category.
     *
     * @return array\entity
     * @access public
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     * @since  2012-03-15
     */
    public function getAllEditorUsers();

    /**
     * we return the user enity associated to the user token and the application.
     *
     * @param string $token
     * @param string $application
     *
     * @return string
     * @access public
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function getUserByTokenAndApplication($token, $application);

    /**
     * we return the user enity associated to the user token and the application.
     *
     * @param string $token
     * @param string $application
     *
     * @return string
     * @access public
     * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
     */
    public function getUserByTokenAndApplicationMultiple($token, $application);
}
