<?php
/**
 * Komaks_NewsEdit extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Komaks
 * @package        Komaks_NewsEdit
 * @copyright      Copyright (c) 2016
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Frontend observer
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
class Komaks_NewsEdit_Model_Observer
{
    /**
     * add items to main menu
     *
     * @access public
     * @param Varien_Event_Observer $observer
     * @return array()
     * @author Ultimate Module Creator
     */
    public function addItemsToTopmenuItems($observer)
    {
        $menu = $observer->getMenu();
        $tree = $menu->getTree();
        $action = Mage::app()->getFrontController()->getAction()->getFullActionName();
        $sectionNodeId = 'section';
        $data = array(
            'name' => Mage::helper('komaks_newsedit')->__('Sections'),
            'id' => $sectionNodeId,
            'url' => Mage::helper('komaks_newsedit/section')->getSectionsUrl(),
            'is_active' => ($action == 'komaks_newsedit_section_index' || $action == 'komaks_newsedit_section_view')
        );
        $sectionNode = new Varien_Data_Tree_Node($data, 'id', $tree, $menu);
        $menu->addChild($sectionNode);
        return $this;
    }
}
