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
 * Author abstract REST API handler model
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
abstract class Komaks_NewsEdit_Model_Api2_Author_Rest extends Komaks_NewsEdit_Model_Api2_Author
{
    /**
     * current author
     */
    protected $_author;

    /**
     * retrieve entity
     *
     * @access protected
     * @return array|mixed
     * @author Ultimate Module Creator
     */
    protected function _retrieve() {
        $author = $this->_getAuthor();
        $this->_prepareAuthorForResponse($author);
        return $author->getData();
    }

    /**
     * get collection
     *
     * @access protected
     * @return array
     * @author Ultimate Module Creator
     */
    protected function _retrieveCollection() {
        $collection = Mage::getResourceModel('komaks_newsedit/author_collection');
        $entityOnlyAttributes = $this->getEntityOnlyAttributes(
            $this->getUserType(),
            Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ
        );
        $availableAttributes = array_keys($this->getAvailableAttributes(
            $this->getUserType(),
            Mage_Api2_Model_Resource::OPERATION_ATTRIBUTE_READ)
        );
        $collection->addFieldToFilter('status', array('eq' => 1));
        $this->_applyCollectionModifiers($collection);
        $authors = $collection->load();
        $authors->walk('afterLoad');
        foreach ($authors as $author) {
            $this->_setAuthor($author);
            $this->_prepareAuthorForResponse($author);
        }
        $authorsArray = $authors->toArray();
        $authorsArray = $authorsArray['items'];

        return $authorsArray;
    }

    /**
     * prepare author for response
     *
     * @access protected
     * @param Komaks_NewsEdit_Model_Author $author
     * @author Ultimate Module Creator
     */
    protected function _prepareAuthorForResponse(Komaks_NewsEdit_Model_Author $author) {
        $authorData = $author->getData();
        if ($this->getActionType() == self::ACTION_TYPE_ENTITY) {
            $authorData['url'] = $author->getAuthorUrl();
        }
    }

    /**
     * create author
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
     * update author
     *
     * @access protected
     * @param array $data
     * @author Ultimate Module Creator
     */
    protected function _update(array $data) {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * delete author
     *
     * @access protected
     * @author Ultimate Module Creator
     */
    protected function _delete() {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * delete current author
     *
     * @access protected
     * @param Komaks_NewsEdit_Model_Author $author
     * @author Ultimate Module Creator
     */
    protected function _setAuthor(Komaks_NewsEdit_Model_Author $author) {
        $this->_author = $author;
    }

    /**
     * get current author
     *
     * @access protected
     * @return Komaks_NewsEdit_Model_Author
     * @author Ultimate Module Creator
     */
    protected function _getAuthor() {
        if (is_null($this->_author)) {
            $authorId = $this->getRequest()->getParam('id');
            $author = Mage::getModel('komaks_newsedit/author');
            $author->load($authorId);
            if (!($author->getId())) {
                $this->_critical(self::RESOURCE_NOT_FOUND);
            }
            $this->_author = $author;
        }
        return $this->_author;
    }
}
