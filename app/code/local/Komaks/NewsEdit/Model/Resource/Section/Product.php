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
 * Section - product relation model
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
class Komaks_NewsEdit_Model_Resource_Section_Product extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * initialize resource model
     *
     * @access protected
     * @see Mage_Core_Model_Resource_Abstract::_construct()
     * @author Ultimate Module Creator
     */
    protected function  _construct()
    {
        $this->_init('komaks_newsedit/section_product', 'rel_id');
    }

    /**
     * Save section - product relations
     *
     * @access public
     * @param Komaks_NewsEdit_Model_Section $section
     * @param array $data
     * @return Komaks_NewsEdit_Model_Resource_Section_Product
     * @author Ultimate Module Creator
     */
    public function saveSectionRelation($section, $data)
    {
        if (!is_array($data)) {
            $data = array();
        }
        $deleteCondition = $this->_getWriteAdapter()->quoteInto('section_id=?', $section->getId());
        $this->_getWriteAdapter()->delete($this->getMainTable(), $deleteCondition);

        foreach ($data as $productId => $info) {
            $this->_getWriteAdapter()->insert(
                $this->getMainTable(),
                array(
                    'section_id' => $section->getId(),
                    'product_id'    => $productId,
                    'position'      => @$info['position']
                )
            );
        }
        return $this;
    }

    /**
     * Save  product - section relations
     *
     * @access public
     * @param Mage_Catalog_Model_Product $prooduct
     * @param array $data
     * @return Komaks_NewsEdit_Model_Resource_Section_Product
     * @author Ultimate Module Creator
     */
    public function saveProductRelation($product, $sectionIds)
    {
        $oldSections = Mage::helper('komaks_newsedit/product')->getSelectedSections($product);
        $oldSectionIds = array();
        foreach ($oldSections as $section) {
            $oldSectionIds[] = $section->getId();
        }
        $insert = array_diff($sectionIds, $oldSectionIds);
        $delete = array_diff($oldSectionIds, $sectionIds);
        $write = $this->_getWriteAdapter();
        if (!empty($insert)) {
            $data = array();
            foreach ($insert as $sectionId) {
                if (empty($sectionId)) {
                    continue;
                }
                $data[] = array(
                    'section_id' => (int)$sectionId,
                    'product_id'  => (int)$product->getId(),
                    'position'=> 1
                );
            }
            if ($data) {
                $write->insertMultiple($this->getMainTable(), $data);
            }
        }
        if (!empty($delete)) {
            foreach ($delete as $sectionId) {
                $where = array(
                    'product_id = ?'  => (int)$product->getId(),
                    'section_id = ?' => (int)$sectionId,
                );
                $write->delete($this->getMainTable(), $where);
            }
        }
        return $this;
    }
}
