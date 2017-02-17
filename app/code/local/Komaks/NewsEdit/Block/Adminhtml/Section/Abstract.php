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
 * Section admin block abstract
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
class Komaks_NewsEdit_Block_Adminhtml_Section_Abstract extends Mage_Adminhtml_Block_Template
{
    /**
     * get current section
     *
     * @access public
     * @return Komaks_NewsEdit_Model_Entity
     * @author Ultimate Module Creator
     */
    public function getSection()
    {
        return Mage::registry('section');
    }

    /**
     * get current section id
     *
     * @access public
     * @return int
     * @author Ultimate Module Creator
     */
    public function getSectionId()
    {
        if ($this->getSection()) {
            return $this->getSection()->getId();
        }
        return null;
    }

    /**
     * get current section Name
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getSectionName()
    {
        return $this->getSection()->getName();
    }

    /**
     * get current section path
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getSectionPath()
    {
        if ($this->getSection()) {
            return $this->getSection()->getPath();
        }
        return Mage::helper('komaks_newsedit/section')->getRootSectionId();
    }

    /**
     * check if there is a root section
     *
     * @access public
     * @return bool
     * @author Ultimate Module Creator
     */
    public function hasRootSection()
    {
        $root = $this->getRoot();
        if ($root && $root->getId()) {
            return true;
        }
        return false;
    }

    /**
     * get the root
     *
     * @access public
     * @param Komaks_NewsEdit_Model_Section|null $parentNodeSection
     * @param int $recursionLevel
     * @return Varien_Data_Tree_Node
     * @author Ultimate Module Creator
     */
    public function getRoot($parentNodeSection = null, $recursionLevel = 3)
    {
        if (!is_null($parentNodeSection) && $parentNodeSection->getId()) {
            return $this->getNode($parentNodeSection, $recursionLevel);
        }
        $root = Mage::registry('root');
        if (is_null($root)) {
            $rootId = Mage::helper('komaks_newsedit/section')->getRootSectionId();
            $tree = Mage::getResourceSingleton('komaks_newsedit/section_tree')
                ->load(null, $recursionLevel);
            if ($this->getSection()) {
                $tree->loadEnsuredNodes($this->getSection(), $tree->getNodeById($rootId));
            }
            $tree->addCollectionData($this->getSectionCollection());
            $root = $tree->getNodeById($rootId);
            if ($root && $rootId != Mage::helper('komaks_newsedit/section')->getRootSectionId()) {
                $root->setIsVisible(true);
            } elseif ($root && $root->getId() == Mage::helper('komaks_newsedit/section')->getRootSectionId()) {
                $root->setName(Mage::helper('komaks_newsedit')->__('Root'));
            }
            Mage::register('root', $root);
        }
        return $root;
    }

    /**
     * Get and register sections root by specified sections IDs
     *
     * @accsess public
     * @param array $ids
     * @return Varien_Data_Tree_Node
     * @author Ultimate Module Creator
     */
    public function getRootByIds($ids)
    {
        $root = Mage::registry('root');
        if (null === $root) {
            $sectionTreeResource = Mage::getResourceSingleton('komaks_newsedit/section_tree');
            $ids     = $sectionTreeResource->getExistingSectionIdsBySpecifiedIds($ids);
            $tree   = $sectionTreeResource->loadByIds($ids);
            $rootId = Mage::helper('komaks_newsedit/section')->getRootSectionId();
            $root   = $tree->getNodeById($rootId);
            if ($root && $rootId != Mage::helper('komaks_newsedit/section')->getRootSectionId()) {
                $root->setIsVisible(true);
            } elseif ($root && $root->getId() == Mage::helper('komaks_newsedit/section')->getRootSectionId()) {
                $root->setName(Mage::helper('komaks_newsedit')->__('Root'));
            }
            $tree->addCollectionData($this->getSectionCollection());
            Mage::register('root', $root);
        }
        return $root;
    }

    /**
     * get specific node
     *
     * @access public
     * @param Komaks_NewsEdit_Model_Section $parentNodeSection
     * @param $int $recursionLevel
     * @return Varien_Data_Tree_Node
     * @author Ultimate Module Creator
     */
    public function getNode($parentNodeSection, $recursionLevel = 2)
    {
        $tree = Mage::getResourceModel('komaks_newsedit/section_tree');
        $nodeId     = $parentNodeSection->getId();
        $parentId   = $parentNodeSection->getParentId();
        $node = $tree->loadNode($nodeId);
        $node->loadChildren($recursionLevel);
        if ($node && $nodeId != Mage::helper('komaks_newsedit/section')->getRootSectionId()) {
            $node->setIsVisible(true);
        } elseif ($node && $node->getId() == Mage::helper('komaks_newsedit/section')->getRootSectionId()) {
            $node->setName(Mage::helper('komaks_newsedit')->__('Root'));
        }
        $tree->addCollectionData($this->getSectionCollection());
        return $node;
    }

    /**
     * get url for saving data
     *
     * @access public
     * @param array $args
     * @return string
     * @author Ultimate Module Creator
     */
    public function getSaveUrl(array $args = array())
    {
        $params = array('_current'=>true);
        $params = array_merge($params, $args);
        return $this->getUrl('*/*/save', $params);
    }

    /**
     * get url for edit
     *
     * @access public
     * @param array $args
     * @return string
     * @author Ultimate Module Creator
     */
    public function getEditUrl()
    {
        return $this->getUrl(
            "*/newsedit_section/edit",
            array('_current' => true, '_query'=>false, 'id' => null, 'parent' => null)
        );
    }

    /**
     * Return root ids
     *
     * @access public
     * @return array
     * @author Ultimate Module Creator
     */
    public function getRootIds()
    {
        return array(Mage::helper('komaks_newsedit/section')->getRootSectionId());
    }
}
