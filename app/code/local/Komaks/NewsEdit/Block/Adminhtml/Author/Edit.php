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
 * Author admin edit form
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
class Komaks_NewsEdit_Block_Adminhtml_Author_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * constructor
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'komaks_newsedit';
        $this->_controller = 'adminhtml_author';
        $this->_updateButton(
            'save',
            'label',
            Mage::helper('komaks_newsedit')->__('Save Author')
        );
        $this->_updateButton(
            'delete',
            'label',
            Mage::helper('komaks_newsedit')->__('Delete Author')
        );
        $this->_addButton(
            'saveandcontinue',
            array(
                'label'   => Mage::helper('komaks_newsedit')->__('Save And Continue Edit'),
                'onclick' => 'saveAndContinueEdit()',
                'class'   => 'save',
            ),
            -100
        );
        $this->_formScripts[] = "
            function saveAndContinueEdit() {
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    /**
     * get the edit form header
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getHeaderText()
    {
        if (Mage::registry('current_author') && Mage::registry('current_author')->getId()) {
            return Mage::helper('komaks_newsedit')->__(
                "Edit Author '%s'",
                $this->escapeHtml(Mage::registry('current_author')->getName())
            );
        } else {
            return Mage::helper('komaks_newsedit')->__('Add Author');
        }
    }
}
