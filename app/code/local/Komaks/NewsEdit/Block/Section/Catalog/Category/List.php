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
 * Section category list
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
class Komaks_NewsEdit_Block_Section_Catalog_Category_List extends Mage_Core_Block_Template
{
    /**
     * get the list of products
     *
     * @access public
     * @return Mage_Catalog_Model_Resource_Category_Collection
     * @author Ultimate Module Creator
     */
    public function getCategoryCollection()
    {
        $collection = $this->getSection()->getSelectedCategoriesCollection();
        $collection->addAttributeToSelect('name');
        $collection->getSelect()->order('related.position');
        $collection->addAttributeToFilter('is_active', 1);
        return $collection;
    }

    /**
     * get current section
     *
     * @access public
     * @return Komaks_NewsEdit_Model_Section
     * @author Ultimate Module Creator
     */
    public function getSection()
    {
        return Mage::registry('current_section');
    }
}
