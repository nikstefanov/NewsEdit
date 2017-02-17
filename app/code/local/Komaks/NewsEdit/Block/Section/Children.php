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
 * Section children list block
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
class Komaks_NewsEdit_Block_Section_Children extends Komaks_NewsEdit_Block_Section_List
{
    /**
     * prepare the layout
     *
     * @access protected
     * @return Komaks_NewsEdit_Block_Section_Children
     * @author Ultimate Module Creator
     */
    protected function _prepareLayout()
    {
        $this->getSections()->addFieldToFilter('parent_id', $this->getCurrentSection()->getId());
        return $this;
    }

    /**
     * get the current section
     *
     * @access protected
     * @return Komaks_NewsEdit_Model_Section
     * @author Ultimate Module Creator
     */
    public function getCurrentSection()
    {
        return Mage::registry('current_section');
    }
}
