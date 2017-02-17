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
 * Category helper
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
class Komaks_NewsEdit_Helper_Category extends Komaks_NewsEdit_Helper_Data
{

    /**
     * get the selected articles for a category
     *
     * @access public
     * @param Mage_Catalog_Model_Category $category
     * @return array()
     * @author Ultimate Module Creator
     */
    public function getSelectedArticles(Mage_Catalog_Model_Category $category)
    {
        if (!$category->hasSelectedArticles()) {
            $articles = array();
            foreach ($this->getSelectedArticlesCollection($category) as $article) {
                $articles[] = $article;
            }
            $category->setSelectedArticles($articles);
        }
        return $category->getData('selected_articles');
    }

    /**
     * get article collection for a category
     *
     * @access public
     * @param Mage_Catalog_Model_Category $category
     * @return Komaks_NewsEdit_Model_Resource_Article_Collection
     * @author Ultimate Module Creator
     */
    public function getSelectedArticlesCollection(Mage_Catalog_Model_Category $category)
    {
        $collection = Mage::getResourceSingleton('komaks_newsedit/article_collection')
            ->addCategoryFilter($category);
        return $collection;
    }

    /**
     * get the selected sections for a category
     *
     * @access public
     * @param Mage_Catalog_Model_Category $category
     * @return array()
     * @author Ultimate Module Creator
     */
    public function getSelectedSections(Mage_Catalog_Model_Category $category)
    {
        if (!$category->hasSelectedSections()) {
            $sections = array();
            foreach ($this->getSelectedSectionsCollection($category) as $section) {
                $sections[] = $section;
            }
            $category->setSelectedSections($sections);
        }
        return $category->getData('selected_sections');
    }

    /**
     * get section collection for a category
     *
     * @access public
     * @param Mage_Catalog_Model_Category $category
     * @return Komaks_NewsEdit_Model_Resource_Section_Collection
     * @author Ultimate Module Creator
     */
    public function getSelectedSectionsCollection(Mage_Catalog_Model_Category $category)
    {
        $collection = Mage::getResourceSingleton('komaks_newsedit/section_collection')
            ->addCategoryFilter($category);
        return $collection;
    }
}
