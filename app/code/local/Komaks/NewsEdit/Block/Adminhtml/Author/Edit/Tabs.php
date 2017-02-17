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
 * Author admin edit tabs
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
class Komaks_NewsEdit_Block_Adminhtml_Author_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
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
        $this->setId('author_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('komaks_newsedit')->__('Author'));
    }

    /**
     * before render html
     *
     * @access protected
     * @return Komaks_NewsEdit_Block_Adminhtml_Author_Edit_Tabs
     * @author Ultimate Module Creator
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'form_author',
            array(
                'label'   => Mage::helper('komaks_newsedit')->__('Author'),
                'title'   => Mage::helper('komaks_newsedit')->__('Author'),
                'content' => $this->getLayout()->createBlock(
                    'komaks_newsedit/adminhtml_author_edit_tab_form'
                )
                ->toHtml(),
            )
        );
        $this->addTab(
            'form_meta_author',
            array(
                'label'   => Mage::helper('komaks_newsedit')->__('Meta'),
                'title'   => Mage::helper('komaks_newsedit')->__('Meta'),
                'content' => $this->getLayout()->createBlock(
                    'komaks_newsedit/adminhtml_author_edit_tab_meta'
                )
                ->toHtml(),
            )
        );
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve author entity
     *
     * @access public
     * @return Komaks_NewsEdit_Model_Author
     * @author Ultimate Module Creator
     */
    public function getAuthor()
    {
        return Mage::registry('current_author');
    }
}
