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
 * Article section model
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
class Komaks_NewsEdit_Model_Article_Section extends Mage_Core_Model_Abstract
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
        $this->_init('komaks_newsedit/article_section');
    }

    /**
     * Save data for article - section relation
     * @access public
     * @param  Komaks_NewsEdit_Model_Article $article
     * @return Komaks_NewsEdit_Model_Article_Section
     * @author Ultimate Module Creator
     */
    public function saveArticleRelation($article)
    {
        $data = $article->getSectionsData();
        if (!is_null($data)) {
            $this->_getResource()->saveArticleRelation($article, $data);
        }
        return $this;
    }

    /**
     * get  for article
     *
     * @access public
     * @param Komaks_NewsEdit_Model_Article $article
     * @return Komaks_NewsEdit_Model_Resource_Article_Section_Collection
     * @author Ultimate Module Creator
     */
    public function getSectionsCollection($article)
    {
        $collection = Mage::getResourceModel('komaks_newsedit/article_section_collection')
            ->addArticleFilter($article);
        return $collection;
    }
}
