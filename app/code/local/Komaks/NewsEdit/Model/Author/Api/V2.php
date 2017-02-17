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
class Komaks_NewsEdit_Model_Author_Api_V2 extends Komaks_NewsEdit_Model_Author_Api
{
    /**
     * Author info
     *
     * @access public
     * @param int $authorId
     * @return object
     * @author Ultimate Module Creator
     */
    public function info($authorId)
    {
        $result = parent::info($authorId);
        $result = Mage::helper('api')->wsiArrayPacker($result);
        return $result;
    }
}
