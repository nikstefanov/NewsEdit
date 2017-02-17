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
 * Section model
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
class Komaks_NewsEdit_Model_Section extends Mage_Catalog_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'komaks_newsedit_section';
    const CACHE_TAG = 'komaks_newsedit_section';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'komaks_newsedit_section';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'section';
    protected $_articleInstance = null;
    protected $_productInstance = null;
    protected $_categoryInstance = null;

    /**
     * constructor
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('komaks_newsedit/section');
    }

    /**
     * before save section
     *
     * @access protected
     * @return Komaks_NewsEdit_Model_Section
     * @author Ultimate Module Creator
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $now = Mage::getSingleton('core/date')->gmtDate();
        if ($this->isObjectNew()) {
            $this->setCreatedAt($now);
        }
        $this->setUpdatedAt($now);
        return $this;
    }

    /**
     * get the url to the section details page
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getSectionUrl()
    {
        if ($this->getUrlKey()) {
            $urlKey = '';
            if ($prefix = Mage::getStoreConfig('komaks_newsedit/section/url_prefix')) {
                $urlKey .= $prefix.'/';
            }
            $urlKey .= $this->getUrlKey();
            if ($suffix = Mage::getStoreConfig('komaks_newsedit/section/url_suffix')) {
                $urlKey .= '.'.$suffix;
            }
            return Mage::getUrl('', array('_direct'=>$urlKey));
        }
        return Mage::getUrl('komaks_newsedit/section/view', array('id'=>$this->getId()));
    }

    /**
     * check URL key
     *
     * @access public
     * @param string $urlKey
     * @param bool $active
     * @return mixed
     * @author Ultimate Module Creator
     */
    public function checkUrlKey($urlKey, $active = true)
    {
        return $this->_getResource()->checkUrlKey($urlKey, $active);
    }

    /**
     * get the section Description
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getDescription()
    {
        $description = $this->getData('description');
        $helper = Mage::helper('cms');
        $processor = $helper->getBlockTemplateProcessor();
        $html = $processor->filter($description);
        return $html;
    }

    /**
     * save section relation
     *
     * @access public
     * @return Komaks_NewsEdit_Model_Section
     * @author Ultimate Module Creator
     */
    protected function _afterSave()
    {
        $this->getProductInstance()->saveSectionRelation($this);
        $this->getCategoryInstance()->saveSectionRelation($this);
        $this->getArticleInstance()->saveSectionRelation($this);
        return parent::_afterSave();
    }

    /**
     * get product relation model
     *
     * @access public
     * @return Komaks_NewsEdit_Model_Section_Product
     * @author Ultimate Module Creator
     */
    public function getProductInstance()
    {
        if (!$this->_productInstance) {
            $this->_productInstance = Mage::getSingleton('komaks_newsedit/section_product');
        }
        return $this->_productInstance;
    }

    /**
     * get selected products array
     *
     * @access public
     * @return array
     * @author Ultimate Module Creator
     */
    public function getSelectedProducts()
    {
        if (!$this->hasSelectedProducts()) {
            $products = array();
            foreach ($this->getSelectedProductsCollection() as $product) {
                $products[] = $product;
            }
            $this->setSelectedProducts($products);
        }
        return $this->getData('selected_products');
    }

    /**
     * Retrieve collection selected products
     *
     * @access public
     * @return Komaks_NewsEdit_Resource_Section_Product_Collection
     * @author Ultimate Module Creator
     */
    public function getSelectedProductsCollection()
    {
        $collection = $this->getProductInstance()->getProductCollection($this);
        return $collection;
    }

    /**
     * get category relation model
     *
     * @access public
     * @return Komaks_NewsEdit_Model_Section_Category
     * @author Ultimate Module Creator
     */
    public function getCategoryInstance()
    {
        if (!$this->_categoryInstance) {
            $this->_categoryInstance = Mage::getSingleton('komaks_newsedit/section_category');
        }
        return $this->_categoryInstance;
    }

    /**
     * get selected categories array
     *
     * @access public
     * @return array
     * @author Ultimate Module Creator
     */
    public function getSelectedCategories()
    {
        if (!$this->hasSelectedCategories()) {
            $categories = array();
            foreach ($this->getSelectedCategoriesCollection() as $category) {
                $categories[] = $category;
            }
            $this->setSelectedCategories($categories);
        }
        return $this->getData('selected_categories');
    }

    /**
     * Retrieve collection selected categories
     *
     * @access public
     * @return Komaks_NewsEdit_Resource_Section_Category_Collection
     * @author Ultimate Module Creator
     */
    public function getSelectedCategoriesCollection()
    {
        $collection = $this->getCategoryInstance()->getCategoryCollection($this);
        return $collection;
    }

    /**
     * get article relation model
     *
     * @access public
     * @return Komaks_NewsEdit_Model_Section_Article
     * @author Ultimate Module Creator
     */
    public function getArticleInstance()
    {
        if (!$this->_articleInstance) {
            $this->_articleInstance = Mage::getSingleton('komaks_newsedit/section_article');
        }
        return $this->_articleInstance;
    }

    /**
     * get selected  array
     *
     * @access public
     * @return array
     * @author Ultimate Module Creator
     */
    public function getSelectedArticles()
    {
        if (!$this->hasSelectedArticles()) {
            $articles = array();
            foreach ($this->getSelectedArticlesCollection() as $article) {
                $articles[] = $article;
            }
            $this->setSelectedArticles($articles);
        }
        return $this->getData('selected_articles');
    }

    /**
     * Retrieve collection selected 
     *
     * @access public
     * @return Komaks_NewsEdit_Model_Section_Article_Collection
     * @author Ultimate Module Creator
     */
    public function getSelectedArticlesCollection()
    {
        $collection = $this->getArticleInstance()->getArticlesCollection($this);
        return $collection;
    }

    /**
     * get the tree model
     *
     * @access public
     * @return Komaks_NewsEdit_Model_Resource_Section_Tree
     * @author Ultimate Module Creator
     */
    public function getTreeModel()
    {
        return Mage::getResourceModel('komaks_newsedit/section_tree');
    }

    /**
     * get tree model instance
     *
     * @access public
     * @return Komaks_NewsEdit_Model_Resource_Section_Tree
     * @author Ultimate Module Creator
     */
    public function getTreeModelInstance()
    {
        if (is_null($this->_treeModel)) {
            $this->_treeModel = Mage::getResourceSingleton('komaks_newsedit/section_tree');
        }
        return $this->_treeModel;
    }

    /**
     * Move section
     *
     * @access public
     * @param   int $parentId new parent section id
     * @param   int $afterSectionId section id after which we have put current section
     * @return  Komaks_NewsEdit_Model_Section
     * @author Ultimate Module Creator
     */
    public function move($parentId, $afterSectionId)
    {
        $parent = Mage::getModel('komaks_newsedit/section')->load($parentId);
        if (!$parent->getId()) {
            Mage::throwException(
                Mage::helper('komaks_newsedit')->__(
                    'Section move operation is not possible: the new parent section was not found.'
                )
            );
        }
        if (!$this->getId()) {
            Mage::throwException(
                Mage::helper('komaks_newsedit')->__(
                    'Section move operation is not possible: the current section was not found.'
                )
            );
        } elseif ($parent->getId() == $this->getId()) {
            Mage::throwException(
                Mage::helper('komaks_newsedit')->__(
                    'Section move operation is not possible: parent section is equal to child section.'
                )
            );
        }
        $this->setMovedSectionId($this->getId());
        $eventParams = array(
            $this->_eventObject => $this,
            'parent'            => $parent,
            'section_id'     => $this->getId(),
            'prev_parent_id'    => $this->getParentId(),
            'parent_id'         => $parentId
        );
        $moveComplete = false;
        $this->_getResource()->beginTransaction();
        try {
            $this->getResource()->changeParent($this, $parent, $afterSectionId);
            $this->_getResource()->commit();
            $this->setAffectedSectionIds(array($this->getId(), $this->getParentId(), $parentId));
            $moveComplete = true;
        } catch (Exception $e) {
            $this->_getResource()->rollBack();
            throw $e;
        }
        if ($moveComplete) {
            Mage::app()->cleanCache(array(self::CACHE_TAG));
        }
        return $this;
    }

    /**
     * Get the parent section
     *
     * @access public
     * @return  Komaks_NewsEdit_Model_Section
     * @author Ultimate Module Creator
     */
    public function getParentSection()
    {
        if (!$this->hasData('parent_section')) {
            $this->setData(
                'parent_section',
                Mage::getModel('komaks_newsedit/section')->load($this->getParentId())
            );
        }
        return $this->_getData('parent_section');
    }

    /**
     * Get the parent id
     *
     * @access public
     * @return  int
     * @author Ultimate Module Creator
     */
    public function getParentId()
    {
        $parentIds = $this->getParentIds();
        return intval(array_pop($parentIds));
    }

    /**
     * Get all parent sections ids
     *
     * @access public
     * @return array
     * @author Ultimate Module Creator
     */
    public function getParentIds()
    {
        return array_diff($this->getPathIds(), array($this->getId()));
    }

    /**
     * Get all sections children
     *
     * @access public
     * @param bool $asArray
     * @return mixed (array|string)
     * @author Ultimate Module Creator
     */
    public function getAllChildren($asArray = false)
    {
        $children = $this->getResource()->getAllChildren($this);
        if ($asArray) {
            return $children;
        } else {
            return implode(',', $children);
        }
    }

    /**
     * Get all sections children
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getChildSections()
    {
        return implode(',', $this->getResource()->getChildren($this, false));
    }

    /**
     * check the id
     *
     * @access public
     * @param int $id
     * @return bool
     * @author Ultimate Module Creator
     */
    public function checkId($id)
    {
        return $this->_getResource()->checkId($id);
    }

    /**
     * Get array sections ids which are part of section path
     *
     * @access public
     * @return array
     * @author Ultimate Module Creator
     */
    public function getPathIds()
    {
        $ids = $this->getData('path_ids');
        if (is_null($ids)) {
            $ids = explode('/', $this->getPath());
            $this->setData('path_ids', $ids);
        }
        return $ids;
    }

    /**
     * Retrieve level
     *
     * @access public
     * @return int
     * @author Ultimate Module Creator
     */
    public function getLevel()
    {
        if (!$this->hasLevel()) {
            return count(explode('/', $this->getPath())) - 1;
        }
        return $this->getData('level');
    }

    /**
     * Verify section ids
     *
     * @access public
     * @param array $ids
     * @return bool
     * @author Ultimate Module Creator
     */
    public function verifyIds(array $ids)
    {
        return $this->getResource()->verifyIds($ids);
    }

    /**
     * check if section has children
     *
     * @access public
     * @return bool
     * @author Ultimate Module Creator
     */
    public function hasChildren()
    {
        return $this->_getResource()->getChildrenAmount($this) > 0;
    }

    /**
     * check if section can be deleted
     *
     * @access protected
     * @return Komaks_NewsEdit_Model_Section
     * @author Ultimate Module Creator
     */
    protected function _beforeDelete()
    {
        if ($this->getResource()->isForbiddenToDelete($this->getId())) {
            Mage::throwException(Mage::helper('komaks_newsedit')->__("Can't delete root section."));
        }
        return parent::_beforeDelete();
    }

    /**
     * get the sections
     *
     * @access public
     * @param Komaks_NewsEdit_Model_Section $parent
     * @param int $recursionLevel
     * @param bool $sorted
     * @param bool $asCollection
     * @param bool $toLoad
     * @author Ultimate Module Creator
     */
    public function getSections($parent, $recursionLevel = 0, $sorted=false, $asCollection=false, $toLoad=true)
    {
        return $this->getResource()->getSections($parent, $recursionLevel, $sorted, $asCollection, $toLoad);
    }

    /**
     * Return parent sections of current section
     *
     * @access public
     * @return array
     * @author Ultimate Module Creator
     */
    public function getParentSections()
    {
        return $this->getResource()->getParentSections($this);
    }

    /**
     * Return children sections of current section
     *
     * @access public
     * @return array
     * @author Ultimate Module Creator
     */
    public function getChildrenSections()
    {
        return $this->getResource()->getChildrenSections($this);
    }

    /**
     * check if parents are enabled
     *
     * @access public
     * @return bool
     * @author Ultimate Module Creator
     */
    public function getStatusPath()
    {
        $parents = $this->getParentSections();
        $rootId = Mage::helper('komaks_newsedit/section')->getRootSectionId();
        foreach ($parents as $parent) {
            if ($parent->getId() == $rootId) {
                continue;
            }
            if (!$parent->getStatus()) {
                return false;
            }
        }
        return $this->getStatus();
    }

    /**
     * Retrieve default attribute set id
     *
     * @access public
     * @return int
     * @author Ultimate Module Creator
     */
    public function getDefaultAttributeSetId()
    {
        return $this->getResource()->getEntityType()->getDefaultAttributeSetId();
    }

    /**
     * get attribute text value
     *
     * @access public
     * @param $attributeCode
     * @return string
     * @author Ultimate Module Creator
     */
    public function getAttributeText($attributeCode)
    {
        $text = $this->getResource()
            ->getAttribute($attributeCode)
            ->getSource()
            ->getOptionText($this->getData($attributeCode));
        if (is_array($text)) {
            return implode(', ', $text);
        }
        return $text;
    }

    /**
     * check if comments are allowed
     *
     * @access public
     * @return array
     * @author Ultimate Module Creator
     */
    public function getAllowComments()
    {
        if ($this->getData('allow_comment') == Komaks_NewsEdit_Model_Adminhtml_Source_Yesnodefault::NO) {
            return false;
        }
        if ($this->getData('allow_comment') == Komaks_NewsEdit_Model_Adminhtml_Source_Yesnodefault::YES) {
            return true;
        }
        return Mage::getStoreConfigFlag('komaks_newsedit/section/allow_comment');
    }

    /**
     * get default values
     *
     * @access public
     * @return array
     * @author Ultimate Module Creator
     */
    public function getDefaultValues()
    {
        $values = array();
        $values['status'] = 1;
        $values['in_rss'] = 1;
        $values['allow_comment'] = Komaks_NewsEdit_Model_Adminhtml_Source_Yesnodefault::USE_DEFAULT;
        return $values;
    }
    
}
