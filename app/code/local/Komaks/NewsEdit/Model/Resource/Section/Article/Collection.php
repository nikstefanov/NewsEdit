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
 * Section - Article relation resource model collection
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
class Komaks_NewsEdit_Model_Resource_Section_Article_Collection extends Komaks_NewsEdit_Model_Resource_Article_Collection
{
    /**
     * remember if fields have been joined
     * @var bool
     */
    protected $_joinedFields = false;

    /**
     * join the link table
     *
     * @access public
     * @return Komaks_NewsEdit_Model_Resource_Section_Article_Collection
     * @author Ultimate Module Creator
     */
    public function joinFields()
    {
        if (!$this->_joinedFields) {
            $this->getSelect()->join(
                array('related' => $this->getTable('komaks_newsedit/section_article')),
                'related.article_id = e.entity_id',
                array('position')
            );
            $this->_joinedFields = true;
        }
        return $this;
    }

    /**
     * add section filter
     *
     * @access public
     * @param Komaks_NewsEdit_Model_Section | int $section
     * @return Komaks_NewsEdit_Model_Resource_Section_Article_Collection
     * @author Ultimate Module Creator
     */
    public function addSectionFilter($section)
    {
        if ($section instanceof Komaks_NewsEdit_Model_Section) {
            $section = $section->getId();
        }
        if (!$this->_joinedFields) {
            $this->joinFields();
        }
        $this->getSelect()->where('related.section_id = ?', $section);
        return $this;
    }
}
