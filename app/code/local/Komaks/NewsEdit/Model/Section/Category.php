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
 * Section category model
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
class Komaks_NewsEdit_Model_Section_Category extends Mage_Core_Model_Abstract
{
    /**
     * Initialize resource
     *
     * @access protected
     * @return void
     * @author Ultimate Module Creator
     */
    protected function _construct()
    {
        $this->_init('komaks_newsedit/section_category');
    }

    /**
     * Save data for section-category relation
     *
     * @access public
     * @param  Komaks_NewsEdit_Model_Section $section
     * @return Komaks_NewsEdit_Model_Section_Category
     * @author Ultimate Module Creator
     */
    public function saveSectionRelation($section)
    {
        $data = $section->getCategoriesData();
        if (!is_null($data)) {
            $this->_getResource()->saveSectionRelation($section, $data);
        }
        return $this;
    }

    /**
     * get categories for section
     *
     * @access public
     * @param Komaks_NewsEdit_Model_Section $section
     * @return Komaks_NewsEdit_Model_Resource_Section_Category_Collection
     * @author Ultimate Module Creator
     */
    public function getCategoryCollection($section)
    {
        $collection = Mage::getResourceModel('komaks_newsedit/section_category_collection')
            ->addSectionFilter($section);
        return $collection;
    }
}
