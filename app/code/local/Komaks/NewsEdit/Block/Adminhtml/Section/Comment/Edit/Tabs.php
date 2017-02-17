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
 * Section comment admin edit tabs
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
class Komaks_NewsEdit_Block_Adminhtml_Section_Comment_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize Tabs
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('section_comment_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('komaks_newsedit')->__('Section Comment'));
    }

    /**
     * before render html
     *
     * @access protected
     * @return Komaks_NewsEdit_Block_Adminhtml_Section_Edit_Tabs
     * @author Ultimate Module Creator
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'form_section_comment',
            array(
                'label'   => Mage::helper('komaks_newsedit')->__('Section comment'),
                'title'   => Mage::helper('komaks_newsedit')->__('Section comment'),
                'content' => $this->getLayout()->createBlock(
                    'komaks_newsedit/adminhtml_section_comment_edit_tab_form'
                )
                ->toHtml(),
            )
        );
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addTab(
                'form_store_section_comment',
                array(
                    'label'   => Mage::helper('komaks_newsedit')->__('Store views'),
                    'title'   => Mage::helper('komaks_newsedit')->__('Store views'),
                    'content' => $this->getLayout()->createBlock(
                        'komaks_newsedit/adminhtml_section_comment_edit_tab_stores'
                    )
                    ->toHtml(),
                )
            );
        }
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve comment
     *
     * @access public
     * @return Komaks_NewsEdit_Model_Section_Comment
     * @author Ultimate Module Creator
     */
    public function getComment()
    {
        return Mage::registry('current_comment');
    }
}
