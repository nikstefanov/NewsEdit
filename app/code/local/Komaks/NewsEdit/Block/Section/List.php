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
 * Section list block
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author Ultimate Module Creator
 */
class Komaks_NewsEdit_Block_Section_List extends Mage_Core_Block_Template
{
    /**
     * initialize
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function _construct()
    {
        parent::_construct();
        $sections = Mage::getResourceModel('komaks_newsedit/section_collection')
                         ->setStoreId(Mage::app()->getStore()->getId())
                         ->addAttributeToSelect('*')
                         ->addAttributeToFilter('status', 1);
        ;
        $sections->getSelect()->order('e.position');
        $this->setSections($sections);
    }

    /**
     * prepare the layout
     *
     * @access protected
     * @return Komaks_NewsEdit_Block_Section_List
     * @author Ultimate Module Creator
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->getSections()->addFieldToFilter('level', 1);
        if ($this->_getDisplayMode() == 0) {
            $pager = $this->getLayout()->createBlock(
                'page/html_pager',
                'komaks_newsedit.sections.html.pager'
            )
            ->setCollection($this->getSections());
            $this->setChild('pager', $pager);
            $this->getSections()->load();
        }
        return $this;
    }

    /**
     * get the pager html
     *
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * get the display mode
     *
     * @access protected
     * @return int
     * @author Ultimate Module Creator
     */
    protected function _getDisplayMode()
    {
        return Mage::getStoreConfigFlag('komaks_newsedit/section/tree');
    }

    /**
     * draw section
     *
     * @access public
     * @param Komaks_NewsEdit_Model_Section
     * @param int $level
     * @return int
     * @author Ultimate Module Creator
     */
    public function drawSection($section, $level = 0)
    {
        $html = '';
        $recursion = $this->getRecursion();
        if ($recursion !== '0' && $level >= $recursion) {
            return '';
        }
        if (!$section->getStatus()) {
            return '';
        }
        $section->setStoreId(Mage::app()->getStore()->getId());
        $children = $section->getChildrenSections()->addAttributeToSelect('*');
        $activeChildren = array();
        if ($recursion == 0 || $level < $recursion-1) {
            foreach ($children as $child) {
                if ($child->getStatus()) {
                    $activeChildren[] = $child;
                }
            }
        }
        $html .= '<li>';
        $html .= '<a href="'.$section->getSectionUrl().'">'.$section->getName().'</a>';
        if (count($activeChildren) > 0) {
            $html .= '<ul>';
            foreach ($children as $child) {
                $html .= $this->drawSection($child, $level+1);
            }
            $html .= '</ul>';
        }
        $html .= '</li>';
        return $html;
    }

    /**
     * get recursion
     *
     * @access public
     * @return int
     * @author Ultimate Module Creator
     */
    public function getRecursion()
    {
        if (!$this->hasData('recursion')) {
            $this->setData('recursion', Mage::getStoreConfig('komaks_newsedit/section/recursion'));
        }
        return $this->getData('recursion');
    }
}
