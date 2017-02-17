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
 * Article abstract REST API handler model
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
abstract class Komaks_NewsEdit_Model_Api2_Article_Rest extends Komaks_NewsEdit_Model_Api2_Article
{
    /**
     * current article
     */
    protected $_article;

    /**
     * retrieve entity
     *
     * @access protected
     * @return array|mixed
     * @author Ultimate Module Creator
     */
    protected function _retrieve() {
        $article = $this->_getArticle();
        $this->_prepareArticleForResponse($article);
        return $article->getData();
    }

    /**
     * get collection
     *
     * @access protected
     * @return array
     * @author Ultimate Module Creator
     */
    protected function _retrieveCollection() {
        $collection = Mage::getResourceModel('komaks_newsedit/article_collection')->addAttributeToSelect('*');
        $collection->setStoreId($this->_getStore()->getId());
        $entityOnlyAttributes = $this->getEntityOnlyAttributes(
            $this->getUserType(),
            Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ
        );
        $availableAttributes = array_keys($this->getAvailableAttributes(
            $this->getUserType(),
            Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ)
        );
        $collection->addAttributeToFilter('status', array('eq' => 1));
        $this->_applyCollectionModifiers($collection);
        $articles = $collection->load();
        $articles->walk('afterLoad');
        foreach ($articles as $article) {
            $this->_setArticle($article);
            $this->_prepareArticleForResponse($article);
        }
        $articlesArray = $articles->toArray();
        return $articlesArray;
    }

    /**
     * prepare article for response
     *
     * @access protected
     * @param Komaks_NewsEdit_Model_Article $article
     * @author Ultimate Module Creator
     */
    protected function _prepareArticleForResponse(Komaks_NewsEdit_Model_Article $article) {
        $articleData = $article->getData();
        if ($this->getActionType() == self::ACTION_TYPE_ENTITY) {
            $articleData['url'] = $article->getArticleUrl();
        }
    }

    /**
     * create article
     *
     * @access protected
     * @param array $data
     * @return string|void
     * @author Ultimate Module Creator
     */
    protected function _create(array $data) {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * update article
     *
     * @access protected
     * @param array $data
     * @author Ultimate Module Creator
     */
    protected function _update(array $data) {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * delete article
     *
     * @access protected
     * @author Ultimate Module Creator
     */
    protected function _delete() {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * delete current article
     *
     * @access protected
     * @param Komaks_NewsEdit_Model_Article $article
     * @author Ultimate Module Creator
     */
    protected function _setArticle(Komaks_NewsEdit_Model_Article $article) {
        $this->_article = $article;
    }

    /**
     * get current article
     *
     * @access protected
     * @return Komaks_NewsEdit_Model_Article
     * @author Ultimate Module Creator
     */
    protected function _getArticle() {
        if (is_null($this->_article)) {
            $articleId = $this->getRequest()->getParam('id');
            $article = Mage::getModel('komaks_newsedit/article');
            $storeId = $this->_getStore()->getId();
            $article->setStoreId($storeId);
            $article->load($articleId);
            if (!($article->getId())) {
                $this->_critical(self::RESOURCE_NOT_FOUND);
            }
            $this->_article = $article;
        }
        return $this->_article;
    }
}
