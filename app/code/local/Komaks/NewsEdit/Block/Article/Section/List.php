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
 * Article Sections list block
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
class Komaks_NewsEdit_Block_Article_Section_List extends Komaks_NewsEdit_Block_Section_List
{
    /**
     * initialize
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function __construct()
    {
        parent::__construct();
        $article = $this->getArticle();
         if ($article) {
             $this->getSections()->addArticleFilter($article->getId());
             $this->getSections()->unshiftOrder('related_article.position', 'ASC');
         }
    }

    /**
     * prepare the layout
     *
     * @access protected
     * @return Komaks_NewsEdit_Block_Article_Section_List
     * @author Ultimate Module Creator
     */
    protected function _prepareLayout()
    {
        return $this;
    }

    /**
     * get the current article
     *
     * @access public
     * @return Komaks_NewsEdit_Model_Article
     * @author Ultimate Module Creator
     */
    public function getArticle()
    {
        return Mage::registry('current_article');
    }
}
