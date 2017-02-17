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
 * Section list on category page block
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
class Komaks_NewsEdit_Block_Catalog_Category_List_Section extends Mage_Core_Block_Template
{
    /**
     * get the list of sections
     *
     * @access protected
     * @return Komaks_NewsEdit_Model_Resource_Section_Collection
     * @author Ultimate Module Creator
     */
    public function getSectionCollection()
    {
        if (!$this->hasData('section_collection')) {
            $category = Mage::registry('current_category');
            $collection = Mage::getResourceSingleton('komaks_newsedit/section_collection')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('status', 1)
                ->addCategoryFilter($category);
            $collection->getSelect()->order('related_category.position', 'ASC');
            $this->setData('section_collection', $collection);
        }
        return $this->getData('section_collection');
    }
}
