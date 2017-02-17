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
 * article - section relation edit block
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
class Komaks_NewsEdit_Block_Adminhtml_Article_Edit_Tab_Section extends Komaks_NewsEdit_Block_Adminhtml_Section_Tree
{
    protected $_sectionIds = null;
    protected $_selectedNodes = null;

    /**
     * constructor
     * Specify template to use
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('komaks_newsedit/article/edit/tab/section.phtml');
    }

    /**
     * Retrieve currently edited article
     *
     * @access public
     * @return Komaks_NewsEdit_Model_Article
     * @author Ultimate Module Creator
     */
    public function getArticle()
    {
        return Mage::registry('current_article');
    }

    /**
     * Return array with  IDs which the article is linked to
     *
     * @access public
     * @return array
     * @author Ultimate Module Creator
     */
    public function getSectionIds()
    {
        if (is_null($this->_sectionIds)) {
            $sections = $this->getArticle()->getSelectedSections();
            $ids = array();
            foreach ($sections as $section) {
                $ids[] = $section->getId();
            }
            $this->_sectionIds = $ids;
        }
        return $this->_sectionIds;
    }

    /**
     * Forms string out of getSectionIds()
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getIdsString()
    {
        return implode(',', $this->getSectionIds());
    }

    /**
     * Returns root node and sets 'checked' flag (if necessary)
     *
     * @access public
     * @return Varien_Data_Tree_Node
     * @author Ultimate Module Creator
     */
    public function getRootNode()
    {
        $root = $this->getRoot();
        if ($root && in_array($root->getId(), $this->getSectionIds())) {
            $root->setChecked(true);
        }
        return $root;
    }

    /**
     * Returns root node
     *
     * @param Komaks_NewsEdit_Model_Section|null $parentNodeSection
     * @param int  $recursionLevel
     * @return Varien_Data_Tree_Node
     * @author Ultimate Module Creator
     */
    public function getRoot($parentNodeSection = null, $recursionLevel = 3)
    {
        if (!is_null($parentNodeSection) && $parentNodeSection->getId()) {
            return $this->getNode($parentNodeSection, $recursionLevel);
        }
        $root = Mage::registry('section_root');
        if (is_null($root)) {
            $rootId = Mage::helper('komaks_newsedit/section')->getRootSectionId();
            $ids    = $this->getSelectedSectionPathIds($rootId);
            $tree   = Mage::getResourceSingleton('komaks_newsedit/section_tree')
                ->loadByIds($ids, false, false);
            if ($this->getSection()) {
                $tree->loadEnsuredNodes($this->getSection(), $tree->getNodeById($rootId));
            }
            $tree->addCollectionData($this->getSectionCollection());
            $root = $tree->getNodeById($rootId);
            Mage::register('section_root', $root);
        }
        return $root;
    }

    /**
     * Returns array with configuration of current node
     *
     * @access public
     * @param Varien_Data_Tree_Node $node
     * @param int $level How deep is the node in the tree
     * @return array
     * @author Ultimate Module Creator
     */
    protected function _getNodeJson($node, $level = 1)
    {
        $item = parent::_getNodeJson($node, $level);
        if ($this->_isParentSelectedSection($node)) {
            $item['expanded'] = true;
        }
        if (in_array($node->getId(), $this->getSectionIds())) {
            $item['checked'] = true;
        }
        return $item;
    }

    /**
     * Returns whether $node is a parent (not exactly direct) of a selected node
     *
     * @access public
     * @param Varien_Data_Tree_Node $node
     * @return bool
     * @author Ultimate Module Creator
     */
    protected function _isParentSelectedSection($node)
    {
        $result = false;
        // Contains string with all section IDs of children (not exactly direct) of the node
        $allChildren = $node->getAllChildren();
        if ($allChildren) {
            $selectedSectionIds = $this->getSectionIds();
            $allChildrenArr = explode(',', $allChildren);
            for ($i = 0, $cnt = count($selectedSectionIds); $i < $cnt; $i++) {
                $isSelf = $node->getId() == $selectedSectionIds[$i];
                if (!$isSelf && in_array($selectedSectionIds[$i], $allChildrenArr)) {
                    $result = true;
                    break;
                }
            }
        }
        return $result;
    }

    /**
     * Returns array with nodes those are selected (contain current article)
     *
     * @access protected
     * @return array
     * @author Ultimate Module Creator
     */
    protected function _getSelectedNodes()
    {
        if ($this->_selectedNodes === null) {
            $this->_selectedNodes = array();
            $root = $this->getRoot();
            foreach ($this->getSectionIds() as $sectionId) {
                if ($root) {
                    $this->_selectedNodes[] = $root->getTree()->getNodeById($sectionId);
                }
            }
        }
        return $this->_selectedNodes;
    }

    /**
     * Returns JSON-encoded array of  children
     *
     * @access public
     * @param int $sectionId
     * @return string
     * @author Ultimate Module Creator
     */
    public function getSectionChildrenJson($sectionId)
    {
        $section = Mage::getModel('komaks_newsedit/section')->load($sectionId);
        $node = $this->getRoot($section, 1)->getTree()->getNodeById($sectionId);
        if (!$node || !$node->hasChildren()) {
            return '[]';
        }
        $children = array();
        foreach ($node->getChildren() as $child) {
            $children[] = $this->_getNodeJson($child);
        }
        return Mage::helper('core')->jsonEncode($children);
    }

    /**
     * Returns URL for loading tree
     *
     * @access public
     * @param null $expanded
     * @return string
     * @author Ultimate Module Creator
     */
    public function getLoadTreeUrl($expanded = null)
    {
        return $this->getUrl('*/*/sectionsJson', array('_current' => true));
    }

    /**
     * Return distinct path ids of selected 
     *
     * @access public
     * @param mixed $rootId Root section Id for context
     * @return array
     * @author Ultimate Module Creator
     */
    public function getSelectedSectionPathIds($rootId = false)
    {
        $ids = array();
        $sectionIds = $this->getSectionIds();
        if (empty($sectionIds)) {
            return array();
        }
        $collection = Mage::getResourceModel('komaks_newsedit/section_collection');
        if ($rootId) {
            $collection->addFieldToFilter('parent_id', $rootId);
        } else {
            $collection->addFieldToFilter('entity_id', array('in' => $sectionIds));
        }

        foreach ($collection as $item) {
            if ($rootId && !in_array($rootId, $item->getPathIds())) {
                continue;
            }
            foreach ($item->getPathIds() as $id) {
                if (!in_array($id, $ids)) {
                    $ids[] = $id;
                }
            }
        }
        return $ids;
    }

    /**
     * Get node label
     *
     * @access public
     * @param Varien_Object $node
     * @return string
     * @author Ultimate Module Creator
     */
    public function buildNodeName($node)
    {
        $result = parent::buildNodeName($node);
        $result .= '<a target="_blank" href="'.
            $this->getUrl(
                'adminhtml/newsedit_section/index',
                array(
                    'id'    => $node->getId(),
                    'clear' => 1
                )
            ).
            '"><em>'.$this->__(' - Edit').'</em></a>';
        return $result;
    }
}
