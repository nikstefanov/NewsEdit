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
 * Section - Categories relation model
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
class Komaks_NewsEdit_Model_Resource_Section_Category extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * initialize resource model
     *
     * @access protected
     * @return void
     * @see Mage_Core_Model_Resource_Abstract::_construct()
     * @author Ultimate Module Creator
     */
    protected function  _construct()
    {
        $this->_init('komaks_newsedit/section_category', 'rel_id');
    }

    /**
     * Save section - category relations
     *
     * @access public
     * @param Komaks_NewsEdit_Model_Section $section
     * @param array $data
     * @return Komaks_NewsEdit_Model_Resource_Section_Category
     * @author Ultimate Module Creator
     */
    public function saveSectionRelation($section, $data)
    {
        if (!is_array($data)) {
            $data = array();
        }
        $deleteCondition = $this->_getWriteAdapter()->quoteInto('section_id=?', $section->getId());
        $this->_getWriteAdapter()->delete($this->getMainTable(), $deleteCondition);

        foreach ($data as $categoryId) {
            if (!empty($categoryId)) {
                $insert = array(
                    'section_id' => $section->getId(),
                    'category_id'   => $categoryId,
                    'position'      => 1
                );
                $this->_getWriteAdapter()->insertOnDuplicate($this->getMainTable(), $insert, array_keys($insert));
            }
        }
        return $this;
    }

    /**
     * Save  category - section relations
     *
     * @access public
     * @param Mage_Catalog_Model_Category $category
     * @param array $data
     * @return Komaks_NewsEdit_Model_Resource_Section_Category
     * @author Ultimate Module Creator
     */
    public function saveCategoryRelation($category, $sectionIds)
    {

        $oldSections = Mage::helper('komaks_newsedit/category')->getSelectedSections($category);
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
                    'category_id'  => (int)$category->getId(),
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
                    'category_id = ?'   => (int)$category->getId(),
                    'section_id = ?' => (int)$sectionId,
                );
                $write->delete($this->getMainTable(), $where);
            }
        }
        return $this;
    }
}
