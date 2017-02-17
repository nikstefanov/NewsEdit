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
 * Product helper
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
class Komaks_NewsEdit_Helper_Product extends Komaks_NewsEdit_Helper_Data
{

    /**
     * get the selected articles for a product
     *
     * @access public
     * @param Mage_Catalog_Model_Product $product
     * @return array()
     * @author Ultimate Module Creator
     */
    public function getSelectedArticles(Mage_Catalog_Model_Product $product)
    {
        if (!$product->hasSelectedArticles()) {
            $articles = array();
            foreach ($this->getSelectedArticlesCollection($product) as $article) {
                $articles[] = $article;
            }
            $product->setSelectedArticles($articles);
        }
        return $product->getData('selected_articles');
    }

    /**
     * get article collection for a product
     *
     * @access public
     * @param Mage_Catalog_Model_Product $product
     * @return Komaks_NewsEdit_Model_Resource_Article_Collection
     * @author Ultimate Module Creator
     */
    public function getSelectedArticlesCollection(Mage_Catalog_Model_Product $product)
    {
        $collection = Mage::getResourceSingleton('komaks_newsedit/article_collection')
            ->addProductFilter($product);
        return $collection;
    }

    /**
     * get the selected sections for a product
     *
     * @access public
     * @param Mage_Catalog_Model_Product $product
     * @return array()
     * @author Ultimate Module Creator
     */
    public function getSelectedSections(Mage_Catalog_Model_Product $product)
    {
        if (!$product->hasSelectedSections()) {
            $sections = array();
            foreach ($this->getSelectedSectionsCollection($product) as $section) {
                $sections[] = $section;
            }
            $product->setSelectedSections($sections);
        }
        return $product->getData('selected_sections');
    }

    /**
     * get section collection for a product
     *
     * @access public
     * @param Mage_Catalog_Model_Product $product
     * @return Komaks_NewsEdit_Model_Resource_Section_Collection
     * @author Ultimate Module Creator
     */
    public function getSelectedSectionsCollection(Mage_Catalog_Model_Product $product)
    {
        $collection = Mage::getResourceSingleton('komaks_newsedit/section_collection')
            ->addProductFilter($product);
        return $collection;
    }
}
