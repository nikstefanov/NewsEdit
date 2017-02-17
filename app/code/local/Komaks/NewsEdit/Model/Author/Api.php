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
class Komaks_NewsEdit_Model_Author_Api extends Mage_Api_Model_Resource_Abstract
{


    /**
     * init author
     *
     * @access protected
     * @param $authorId
     * @return Komaks_NewsEdit_Model_Author
     * @author      Ultimate Module Creator
     */
    protected function _initAuthor($authorId)
    {
        $author = Mage::getModel('komaks_newsedit/author')->load($authorId);
        if (!$author->getId()) {
            $this->_fault('author_not_exists');
        }
        return $author;
    }

    /**
     * get authors
     *
     * @access public
     * @param mixed $filters
     * @return array
     * @author Ultimate Module Creator
     */
    public function items($filters = null)
    {
        $collection = Mage::getModel('komaks_newsedit/author')->getCollection();
        $apiHelper = Mage::helper('api');
        $filters = $apiHelper->parseFilters($filters);
        try {
            foreach ($filters as $field => $value) {
                $collection->addFieldToFilter($field, $value);
            }
        } catch (Mage_Core_Exception $e) {
            $this->_fault('filters_invalid', $e->getMessage());
        }
        $result = array();
        foreach ($collection as $author) {
            $result[] = $this->_getApiData($author);
        }
        return $result;
    }

    /**
     * Add author
     *
     * @access public
     * @param array $data
     * @return array
     * @author Ultimate Module Creator
     */
    public function add($data)
    {
        try {
            if (is_null($data)) {
                throw new Exception(Mage::helper('komaks_newsedit')->__("Data cannot be null"));
            }
            $data = (array)$data;
            $author = Mage::getModel('komaks_newsedit/author')
                ->setData((array)$data)
                ->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        } catch (Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return $author->getId();
    }

    /**
     * Change existing author information
     *
     * @access public
     * @param int $authorId
     * @param array $data
     * @return bool
     * @author Ultimate Module Creator
     */
    public function update($authorId, $data)
    {
        $author = $this->_initAuthor($authorId);
        try {
            $data = (array)$data;
            $author->addData($data);
            $author->save();
        }
        catch (Mage_Core_Exception $e) {
            $this->_fault('save_error', $e->getMessage());
        }

        return true;
    }

    /**
     * remove author
     *
     * @access public
     * @param int $authorId
     * @return bool
     * @author Ultimate Module Creator
     */
    public function remove($authorId)
    {
        $author = $this->_initAuthor($authorId);
        try {
            $author->delete();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('remove_error', $e->getMessage());
        }
        return true;
    }

    /**
     * get info
     *
     * @access public
     * @param int $authorId
     * @return array
     * @author Ultimate Module Creator
     */
    public function info($authorId)
    {
        $result = array();
        $author = $this->_initAuthor($authorId);
        $result = $this->_getApiData($author);
        return $result;
    }

    /**
     * get data for api
     *
     * @access protected
     * @param Komaks_NewsEdit_Model_Author $author
     * @return array()
     * @author Ultimate Module Creator
     */
    protected function _getApiData(Komaks_NewsEdit_Model_Author $author)
    {
        return $author->getData();
    }
}
