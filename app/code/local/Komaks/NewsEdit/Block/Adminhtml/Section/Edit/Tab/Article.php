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
 * section - article relation edit block
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
class Komaks_NewsEdit_Block_Adminhtml_Section_Edit_Tab_Article extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set grid params
     *
     * @access protected
     * @author Ultimate Module Creator
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('article_grid');
        $this->setDefaultSort('position');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        if ($this->getSection()->getId()) {
            $this->setDefaultFilter(array('in_articles' => 1));
        }
    }

    /**
     * prepare the article collection
     *
     * @access protected
     * @return Komaks_NewsEdit_Block_Adminhtml_Section_Edit_Tab_Article
     * @author Ultimate Module Creator
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('komaks_newsedit/article_collection')->addAttributeToSelect('title');
        if ($this->getSection()->getId()) {
            $constraint = 'related.section_id='.$this->getSection()->getId();
        } else {
            $constraint = 'related.section_id=0';
        }
        $collection->getSelect()->joinLeft(
            array('related' => $collection->getTable('komaks_newsedit/section_article')),
            'related.article_id=e.entity_id AND '.$constraint,
            array('position')
        );
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * prepare mass action grid
     *
     * @access protected
     * @return Komaks_NewsEdit_Block_Adminhtml_Section_Edit_Tab_Article
     * @author Ultimate Module Creator
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * prepare the grid columns
     *
     * @access protected
     * @return Komaks_NewsEdit_Block_Adminhtml_Section_Edit_Tab_Article
     * @author Ultimate Module Creator
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_articles',
            array(
                'header_css_class'  => 'a-center',
                'type'              => 'checkbox',
                'name'              => 'in_articles',
                'values'            => $this->_getSelectedArticles(),
                'align'             => 'center',
                'index'             => 'entity_id'
            )
        );
        $this->addColumn(
            'title',
            array(
                'header'    => Mage::helper('komaks_newsedit')->__('Title'),
                'align'     => 'left',
                'index'     => 'title',
                'renderer'  => 'komaks_newsedit/adminhtml_helper_column_renderer_relation',
                'params'    => array(
                    'id'    => 'getId'
                ),
                'base_link' => 'adminhtml/newsedit_article/edit',
            )
        );
        $this->addColumn(
            'position',
            array(
                'header'         => Mage::helper('komaks_newsedit')->__('Position'),
                'name'           => 'position',
                'width'          => 60,
                'type'           => 'number',
                'validate_class' => 'validate-number',
                'index'          => 'position',
                'editable'       => true,
            )
        );
    }

    /**
     * Retrieve selected 
     *
     * @access protected
     * @return array
     * @author Ultimate Module Creator
     */
    protected function _getSelectedArticles()
    {
        $articles = $this->getSectionArticles();
        if (!is_array($articles)) {
            $articles = array_keys($this->getSelectedArticles());
        }
        return $articles;
    }

    /**
     * Retrieve selected {{siblingsLabels}}
     *
     * @access protected
     * @return array
     * @author Ultimate Module Creator
     */
    public function getSelectedArticles()
    {
        $articles = array();
        $selected = Mage::registry('current_section')->getSelectedArticles();
        if (!is_array($selected)) {
            $selected = array();
        }
        foreach ($selected as $article) {
            $articles[$article->getId()] = array('position' => $article->getPosition());
        }
        return $articles;
    }

    /**
     * get row url
     *
     * @access public
     * @param Komaks_NewsEdit_Model_Article
     * @return string
     * @author Ultimate Module Creator
     */
    public function getRowUrl($item)
    {
        return '#';
    }

    /**
     * get grid url
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            '*/*/articlesGrid',
            array(
                'id' => $this->getSection()->getId()
            )
        );
    }

    /**
     * get the current section
     *
     * @access public
     * @return Komaks_NewsEdit_Model_Section
     * @author Ultimate Module Creator
     */
    public function getSection()
    {
        return Mage::registry('current_section');
    }

    /**
     * Add filter
     *
     * @access protected
     * @param object $column
     * @return Komaks_NewsEdit_Block_Adminhtml_Section_Edit_Tab_Article
     * @author Ultimate Module Creator
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_articles') {
            $articleIds = $this->_getSelectedArticles();
            if (empty($articleIds)) {
                $articleIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$articleIds));
            } else {
                if ($articleIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$articleIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
}
