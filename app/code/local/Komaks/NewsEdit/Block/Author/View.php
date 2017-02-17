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
 * Author view block
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
class Komaks_NewsEdit_Block_Author_View extends Mage_Core_Block_Template
{
    /**
     * get the current author
     *
     * @access public
     * @return mixed (Komaks_NewsEdit_Model_Author|null)
     * @author Ultimate Module Creator
     */
    public function getCurrentAuthor()
    {
        return Mage::registry('current_author');
    }
}
