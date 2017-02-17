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
 * Article comment admin edit tabs
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
class Komaks_NewsEdit_Block_Adminhtml_Article_Comment_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
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
        $this->setId('article_comment_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('komaks_newsedit')->__('Article Comment'));
    }

    /**
     * before render html
     *
     * @access protected
     * @return Komaks_NewsEdit_Block_Adminhtml_Article_Edit_Tabs
     * @author Ultimate Module Creator
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'form_article_comment',
            array(
                'label'   => Mage::helper('komaks_newsedit')->__('Article comment'),
                'title'   => Mage::helper('komaks_newsedit')->__('Article comment'),
                'content' => $this->getLayout()->createBlock(
                    'komaks_newsedit/adminhtml_article_comment_edit_tab_form'
                )
                ->toHtml(),
            )
        );
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addTab(
                'form_store_article_comment',
                array(
                    'label'   => Mage::helper('komaks_newsedit')->__('Store views'),
                    'title'   => Mage::helper('komaks_newsedit')->__('Store views'),
                    'content' => $this->getLayout()->createBlock(
                        'komaks_newsedit/adminhtml_article_comment_edit_tab_stores'
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
     * @return Komaks_NewsEdit_Model_Article_Comment
     * @author Ultimate Module Creator
     */
    public function getComment()
    {
        return Mage::registry('current_comment');
    }
}
