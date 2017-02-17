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
class Komaks_NewsEdit_Model_Article_Api_V2 extends Komaks_NewsEdit_Model_Article_Api
{
    /**
     * Article info
     *
     * @access public
     * @param int $articleId
     * @return object
     * @author Ultimate Module Creator
     */
    public function info($articleId)
    {
        $result = parent::info($articleId);
        $result = Mage::helper('api')->wsiArrayPacker($result);
        foreach ($result->products as $key => $value) {
            $result->products[$key] = array('key' => $key, 'value' => $value);
        }
        foreach ($result->categories as $key => $value) {
            $result->categories[$key] = array('key' => $key, 'value' => $value);
        }
        foreach ($result->sections as $key => $value) {
            $result->sections[$key] = array('key' => $key, 'value' => $value);
        }
        return $result;
    }
}
