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
 * Section admin widget controller
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
class Komaks_NewsEdit_Adminhtml_Newsedit_Section_WidgetController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Chooser Source action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function chooserAction()
    {
        $uniqId = $this->getRequest()->getParam('uniq_id');
        $grid = $this->getLayout()->createBlock(
            'komaks_newsedit/adminhtml_section_widget_chooser',
            '',
            array(
                'id' => $uniqId,
            )
        );
        $this->getResponse()->setBody($grid->toHtml());
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
        if ($sectionId = (int) $this->getRequest()->getPost('id')) {
            $section = Mage::getModel('komaks_newsedit/section')->load($sectionId);
            if ($section->getId()) {
                Mage::register('section', $section);
                Mage::register('current_section', $section);
            }
            $this->getResponse()->setBody(
                $this->_getSectionTreeBlock()->getTreeJson($section)
            );
        }
    }

    /**
     * get section tree block
     *
     * @access protected
     * @return Komaks_NewsEdit_Block_Adminhtml_Section_Widget_Chooser
     * @author Ultimate Module Creator
     */
    protected function _getSectionTreeBlock()
    {
        return $this->getLayout()->createBlock(
            'komaks_newsedit/adminhtml_section_widget_chooser',
            '',
            array(
                'id' => $this->getRequest()->getParam('uniq_id'),
                'use_massaction' => $this->getRequest()->getParam('use_massaction', false)
            )
        );
    }
}
