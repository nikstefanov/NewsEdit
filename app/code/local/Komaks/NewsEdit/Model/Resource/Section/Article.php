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
 * Section - Article relation model
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
class Komaks_NewsEdit_Model_Resource_Section_Article extends Mage_Core_Model_Resource_Db_Abstract
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
        $this->_init('komaks_newsedit/section_article', 'rel_id');
    }

    /**
     * Save section - article relations
     *
     * @access public
     * @param Komaks_NewsEdit_Model_Section $section
     * @param array $data
     * @return Komaks_NewsEdit_Model_Resource_Section_Article
     * @author Ultimate Module Creator
     */
    public function saveSectionRelation($section, $data)
    {
        if (!is_array($data)) {
            $data = array();
        }

        $adapter = $this->_getWriteAdapter();
        $bind    = array(
            ':section_id'    => (int)$section->getId(),
        );
        $select = $adapter->select()
            ->from($this->getMainTable(), array('rel_id', 'article_id'))
            ->where('section_id = :section_id');

        $related   = $adapter->fetchPairs($select, $bind);
        $deleteIds = array();
        foreach ($related as $relId => $articleId) {
            if (!isset($data[$articleId])) {
                $deleteIds[] = (int)$relId;
            }
        }
        if (!empty($deleteIds)) {
            $adapter->delete(
                $this->getMainTable(),
                array('rel_id IN (?)' => $deleteIds)
            );
        }

        foreach ($data as $articleId => $info) {
            $adapter->insertOnDuplicate(
                $this->getMainTable(),
                array(
                    'section_id'      => $section->getId(),
                    'article_id'     => $articleId,
                    'position'      => @$info['position']
                ),
                array('position')
            );
        }
        return $this;
    }
}
