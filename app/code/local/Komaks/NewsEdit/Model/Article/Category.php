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
 * Article category model
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
class Komaks_NewsEdit_Model_Article_Category extends Mage_Core_Model_Abstract
{
    /**
     * Initialize resource
     *
     * @access protected
     * @return void
     * @author Ultimate Module Creator
     */
    protected function _construct()
    {
        $this->_init('komaks_newsedit/article_category');
    }

    /**
     * Save data for article-category relation
     *
     * @access public
     * @param  Komaks_NewsEdit_Model_Article $article
     * @return Komaks_NewsEdit_Model_Article_Category
     * @author Ultimate Module Creator
     */
    public function saveArticleRelation($article)
    {
        $data = $article->getCategoriesData();
        if (!is_null($data)) {
            $this->_getResource()->saveArticleRelation($article, $data);
        }
        return $this;
    }

    /**
     * get categories for article
     *
     * @access public
     * @param Komaks_NewsEdit_Model_Article $article
     * @return Komaks_NewsEdit_Model_Resource_Article_Category_Collection
     * @author Ultimate Module Creator
     */
    public function getCategoryCollection($article)
    {
        $collection = Mage::getResourceModel('komaks_newsedit/article_category_collection')
            ->addArticleFilter($article);
        return $collection;
    }
}
