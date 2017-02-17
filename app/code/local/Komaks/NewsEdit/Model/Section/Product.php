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
 * Section product model
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
class Komaks_NewsEdit_Model_Section_Product extends Mage_Core_Model_Abstract
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
        $this->_init('komaks_newsedit/section_product');
    }

    /**
     * Save data for section-product relation
     * @access public
     * @param  Komaks_NewsEdit_Model_Section $section
     * @return Komaks_NewsEdit_Model_Section_Product
     * @author Ultimate Module Creator
     */
    public function saveSectionRelation($section)
    {
        $data = $section->getProductsData();
        if (!is_null($data)) {
            $this->_getResource()->saveSectionRelation($section, $data);
        }
        return $this;
    }

    /**
     * get products for section
     *
     * @access public
     * @param Komaks_NewsEdit_Model_Section $section
     * @return Komaks_NewsEdit_Model_Resource_Section_Product_Collection
     * @author Ultimate Module Creator
     */
    public function getProductCollection($section)
    {
        $collection = Mage::getResourceModel('komaks_newsedit/section_product_collection')
            ->addSectionFilter($section);
        return $collection;
    }
}
