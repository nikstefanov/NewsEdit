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
 * Section Articles list block
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
class Komaks_NewsEdit_Block_Section_Article_List extends Komaks_NewsEdit_Block_Article_List
{
    /**
     * initialize
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function __construct()
    {
        parent::__construct();
        $section = $this->getSection();
         if ($section) {
             $this->getArticles()->addSectionFilter($section->getId());
             $this->getArticles()->unshiftOrder('related_section.position', 'ASC');
         }
    }

    /**
     * prepare the layout
     *
     * @access protected
     * @return Komaks_NewsEdit_Block_Section_Article_List
     * @author Ultimate Module Creator
     */
    protected function _prepareLayout()
    {
        return $this;
    }

    /**
     * get the current section
     *
     * @access public
     * @return Komaks_NewsEdit_Model_Section
     * @author Ultimate Module Creator
     */
    public function getSection()
    {
        return Mage::registry('current_section');
    }
}
