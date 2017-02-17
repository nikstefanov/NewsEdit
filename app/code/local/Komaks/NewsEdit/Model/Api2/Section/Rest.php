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
 * Section abstract REST API handler model
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
abstract class Komaks_NewsEdit_Model_Api2_Section_Rest extends Komaks_NewsEdit_Model_Api2_Section
{
    /**
     * current section
     */
    protected $_section;

    /**
     * retrieve entity
     *
     * @access protected
     * @return array|mixed
     * @author Ultimate Module Creator
     */
    protected function _retrieve() {
        $section = $this->_getSection();
        $this->_prepareSectionForResponse($section);
        return $section->getData();
    }

    /**
     * get collection
     *
     * @access protected
     * @return array
     * @author Ultimate Module Creator
     */
    protected function _retrieveCollection() {
        $collection = Mage::getResourceModel('komaks_newsedit/section_collection')->addAttributeToSelect('*');
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
        $collection->addAttributeToFilter('entity_id', array('neq'=>Mage::helper('komaks_newsedit/section')->getRootSectionId()));
        $this->_applyCollectionModifiers($collection);
        $sections = $collection->load();
        $sections->walk('afterLoad');
        foreach ($sections as $section) {
            $this->_setSection($section);
            $this->_prepareSectionForResponse($section);
        }
        $sectionsArray = $sections->toArray();
        return $sectionsArray;
    }

    /**
     * prepare section for response
     *
     * @access protected
     * @param Komaks_NewsEdit_Model_Section $section
     * @author Ultimate Module Creator
     */
    protected function _prepareSectionForResponse(Komaks_NewsEdit_Model_Section $section) {
        $sectionData = $section->getData();
        if ($this->getActionType() == self::ACTION_TYPE_ENTITY) {
            $sectionData['url'] = $section->getSectionUrl();
        }
    }

    /**
     * create section
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
     * update section
     *
     * @access protected
     * @param array $data
     * @author Ultimate Module Creator
     */
    protected function _update(array $data) {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * delete section
     *
     * @access protected
     * @author Ultimate Module Creator
     */
    protected function _delete() {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * delete current section
     *
     * @access protected
     * @param Komaks_NewsEdit_Model_Section $section
     * @author Ultimate Module Creator
     */
    protected function _setSection(Komaks_NewsEdit_Model_Section $section) {
        $this->_section = $section;
    }

    /**
     * get current section
     *
     * @access protected
     * @return Komaks_NewsEdit_Model_Section
     * @author Ultimate Module Creator
     */
    protected function _getSection() {
        if (is_null($this->_section)) {
            $sectionId = $this->getRequest()->getParam('id');
            $section = Mage::getModel('komaks_newsedit/section');
            $storeId = $this->_getStore()->getId();
            $section->setStoreId($storeId);
            $section->load($sectionId);
            if (!($section->getId())) {
                $this->_critical(self::RESOURCE_NOT_FOUND);
            }
            $this->_section = $section;
        }
        return $this->_section;
    }
}
