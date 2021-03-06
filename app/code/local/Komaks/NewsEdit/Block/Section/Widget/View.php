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
 * Section widget block
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
class Komaks_NewsEdit_Block_Section_Widget_View extends Mage_Core_Block_Template implements
    Mage_Widget_Block_Interface
{
    protected $_htmlTemplate = 'komaks_newsedit/section/widget/view.phtml';

    /**
     * Prepare a for widget
     *
     * @access protected
     * @return Komaks_NewsEdit_Block_Section_Widget_View
     * @author Ultimate Module Creator
     */
    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();
        $sectionId = $this->getData('section_id');
        if ($sectionId) {
            $section = Mage::getModel('komaks_newsedit/section')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($sectionId);
            if ($section->getStatusPath()) {
                $this->setCurrentSection($section);
                $this->setTemplate($this->_htmlTemplate);
            }
        }
        return $this;
    }
}
