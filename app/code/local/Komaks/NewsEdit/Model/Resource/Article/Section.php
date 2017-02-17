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
 * Article - Section relation model
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
class Komaks_NewsEdit_Model_Resource_Article_Section extends Mage_Core_Model_Resource_Db_Abstract
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
        $this->_init('komaks_newsedit/article_section', 'rel_id');
    }

    /**
     * Save article - section relations
     *
     * @access public
     * @param Komaks_NewsEdit_Model_Article $article
     * @param array $data
     * @return Komaks_NewsEdit_Model_Resource_Article_Section
     * @author Ultimate Module Creator
     */
    public function saveArticleRelation($article, $sectionIds)
    {
        if (is_null($sectionIds)) {
            return $this;
        }
        $oldSections = $article->getSelectedSections();
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
                    'article_id'  => (int)$article->getId(),
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
                    'article_id = ?'  => (int)$article->getId(),
                    'section_id = ?' => (int)$sectionId,
                );
                $write->delete($this->getMainTable(), $where);
            }
        }
        return $this;
    }
}
