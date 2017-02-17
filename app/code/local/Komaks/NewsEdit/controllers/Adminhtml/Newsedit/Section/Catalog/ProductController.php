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
 * Section - product controller
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
require_once ("Mage/Adminhtml/controllers/Catalog/ProductController.php");
class Komaks_NewsEdit_Adminhtml_Newsedit_Section_Catalog_ProductController extends Mage_Adminhtml_Catalog_ProductController
{

    /**
     * sections action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function sectionsAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * sections json action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function sectionsJsonAction()
    {
        $product = $this->_initProduct();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock(
                'komaks_newsedit/adminhtml_catalog_product_edit_tab_section'
            )
            ->getSectionChildrenJson($this->getRequest()->getParam('section'))
        );
    }
}
