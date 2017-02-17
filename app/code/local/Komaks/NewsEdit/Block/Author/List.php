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
 * Author list block
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author Ultimate Module Creator
 */
class Komaks_NewsEdit_Block_Author_List extends Mage_Core_Block_Template
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
        $authors = Mage::getResourceModel('komaks_newsedit/author_collection')
                         ->addFieldToFilter('status', 1);
        $authors->setOrder('name', 'asc');
        $this->setAuthors($authors);
    }

    /**
     * prepare the layout
     *
     * @access protected
     * @return Komaks_NewsEdit_Block_Author_List
     * @author Ultimate Module Creator
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock(
            'page/html_pager',
            'komaks_newsedit.author.html.pager'
        )
        ->setCollection($this->getAuthors());
        $this->setChild('pager', $pager);
        $this->getAuthors()->load();
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
}
