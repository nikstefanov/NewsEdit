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
 * Adminhtml observer
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
class Komaks_NewsEdit_Model_Adminhtml_Observer
{
    /**
     * check if tab can be added
     *
     * @access protected
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     * @author Ultimate Module Creator
     */
    protected function _canAddTab($product)
    {
        if ($product->getId()) {
            return true;
        }
        if (!$product->getAttributeSetId()) {
            return false;
        }
        $request = Mage::app()->getRequest();
        if ($request->getParam('type') == 'configurable') {
            if ($request->getParam('attributes')) {
                return true;
            }
        }
        return false;
    }

    /**
     * add the article tab to products
     *
     * @access public
     * @param Varien_Event_Observer $observer
     * @return Komaks_NewsEdit_Model_Adminhtml_Observer
     * @author Ultimate Module Creator
     */
    public function addProductArticleBlock($observer)
    {
        $block = $observer->getEvent()->getBlock();
        $product = Mage::registry('product');
        if ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs && $this->_canAddTab($product)) {
            $block->addTab(
                'articles',
                array(
                    'label' => Mage::helper('komaks_newsedit')->__('Articles'),
                    'url'   => Mage::helper('adminhtml')->getUrl(
                        'adminhtml/newsedit_article_catalog_product/articles',
                        array('_current' => true)
                    ),
                    'class' => 'ajax',
                )
            );
        }
        return $this;
    }

    /**
     * add the section tab to products
     *
     * @access public
     * @param Varien_Event_Observer $observer
     * @return Komaks_NewsEdit_Model_Adminhtml_Observer
     * @author Ultimate Module Creator
     */
    public function addProductSectionBlock($observer)
    {
        $block = $observer->getEvent()->getBlock();
        $product = Mage::registry('product');
        if ($block instanceof Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs && $this->_canAddTab($product)) {
            $block->addTab(
                'sections',
                array(
                    'label' => Mage::helper('komaks_newsedit')->__('Sections'),
                    'url'   => Mage::helper('adminhtml')->getUrl(
                        'adminhtml/newsedit_section_catalog_product/sections',
                        array('_current' => true)
                    ),
                    'class' => 'ajax',
                )
            );
        }
        return $this;
    }

    /**
     * save article - product relation
     * @access public
     * @param Varien_Event_Observer $observer
     * @return Komaks_NewsEdit_Model_Adminhtml_Observer
     * @author Ultimate Module Creator
     */
    public function saveProductArticleData($observer)
    {
        $post = Mage::app()->getRequest()->getPost('articles', -1);
        if ($post != '-1') {
            $post = Mage::helper('adminhtml/js')->decodeGridSerializedInput($post);
            $product = Mage::registry('product');
            $articleProduct = Mage::getResourceSingleton('komaks_newsedit/article_product')
                ->saveProductRelation($product, $post);
        }
        return $this;
    }
    /**
     * save section - product relation
     *
     * @access public
     * @param Varien_Event_Observer $observer
     * @return Komaks_NewsEdit_Model_Adminhtml_Observer
     * @author Ultimate Module Creator
     */
    public function saveProductSectionData($observer)
    {
        $post = Mage::app()->getRequest()->getPost('section_ids', -1);
        if ($post != '-1') {
            $post = explode(',', $post);
            $post = array_unique($post);
            $product = $observer->getEvent()->getProduct();
            Mage::getResourceSingleton('komaks_newsedit/section_product')
                ->saveProductRelation($product, $post);
        }
        return $this;
    }

    /**
     * add the article tab to categories
     *
     * @access public
     * @param Varien_Event_Observer $observer
     * @return Komaks_NewsEdit_Model_Adminhtml_Observer
     * @author Ultimate Module Creator
     */
    public function addCategoryArticleBlock($observer)
    {
        $tabs = $observer->getEvent()->getTabs();
        $content = $tabs->getLayout()->createBlock(
            'komaks_newsedit/adminhtml_catalog_category_tab_article',
            'category.article.grid'
        )->toHtml();
        $serializer = $tabs->getLayout()->createBlock(
            'adminhtml/widget_grid_serializer',
            'category.article.grid.serializer'
        );
        $serializer->initSerializerBlock(
            'category.article.grid',
            'getSelectedArticles',
            'articles',
            'category_articles'
        );
        $serializer->addColumnInputName('position');
        $content .= $serializer->toHtml();
        $tabs->addTab(
            'article',
            array(
                'label'   => Mage::helper('komaks_newsedit')->__('Articles'),
                'content' => $content,
            )
        );
        return $this;
    }

    /**
     * add the section tab to categories
     *
     * @access public
     * @param Varien_Event_Observer $observer
     * @return Komaks_NewsEdit_Model_Adminhtml_Observer
     * @author Ultimate Module Creator
     */
    public function addCategorySectionBlock($observer)
    {
        $tabs = $observer->getEvent()->getTabs();
        $content = $tabs->getLayout()->createBlock(
            'komaks_newsedit/adminhtml_catalog_category_tab_section',
            'category.section.grid'
        )->toHtml();
        $tabs->addTab(
            'section',
            array(
                'label'   => Mage::helper('komaks_newsedit')->__('Sections'),
                'content' => $content,
            )
        );
        return $this;
    }

    /**
     * save article - category relation
     *
     * @access public
     * @param Varien_Event_Observer $observer
     * @return Komaks_NewsEdit_Model_Adminhtml_Observer
     * @author Ultimate Module Creator
     */
    public function saveCategoryArticleData($observer)
    {
        $post = Mage::app()->getRequest()->getPost('articles', -1);
        if ($post != '-1') {
            $post = Mage::helper('adminhtml/js')->decodeGridSerializedInput($post);
            $category = Mage::registry('category');
            $articleCategory = Mage::getResourceSingleton('komaks_newsedit/article_category')
                ->saveCategoryRelation($category, $post);
        }
        return $this;
    }

    /**
     * save section - category relation
     *
     * @access public
     * @param Varien_Event_Observer $observer
     * @return Komaks_NewsEdit_Model_Adminhtml_Observer
     * @author Ultimate Module Creator
     */
    public function saveCategorySectionData($observer)
    {
        $post = Mage::app()->getRequest()->getPost('section_ids', -1);
        if ($post != '-1') {
            $post = explode(',', $post);
            $post = array_unique($post);
            $category = $observer->getEvent()->getCategory();
            Mage::getResourceSingleton('komaks_newsedit/section_category')
                ->saveCategoryRelation($category, $post);
        }
        return $this;
    }
}
