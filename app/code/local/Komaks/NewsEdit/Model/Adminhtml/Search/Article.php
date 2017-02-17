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
 * Admin search model
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
class Komaks_NewsEdit_Model_Adminhtml_Search_Article extends Varien_Object
{
    /**
     * Load search results
     *
     * @access public
     * @return Komaks_NewsEdit_Model_Adminhtml_Search_Article
     * @author Ultimate Module Creator
     */
    public function load()
    {
        $arr = array();
        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($arr);
            return $this;
        }
        $collection = Mage::getResourceModel('komaks_newsedit/article_collection')
            ->addAttributeToFilter('title', array('like' => $this->getQuery().'%'))
            ->setCurPage($this->getStart())
            ->setPageSize($this->getLimit())
            ->load();
        foreach ($collection->getItems() as $article) {
            $arr[] = array(
                'id'          => 'article/1/'.$article->getId(),
                'type'        => Mage::helper('komaks_newsedit')->__('Article'),
                'name'        => $article->getTitle(),
                'description' => $article->getTitle(),
                'url' => Mage::helper('adminhtml')->getUrl(
                    '*/newsedit_article/edit',
                    array('id'=>$article->getId())
                ),
            );
        }
        $this->setResults($arr);
        return $this;
    }
}
