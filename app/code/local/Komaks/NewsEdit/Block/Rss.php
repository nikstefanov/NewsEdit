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
 * NewsEdit RSS block
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
class Komaks_NewsEdit_Block_Rss extends Mage_Core_Block_Template
{
    /**
     * RSS feeds for this block
     */
    protected $_feeds = array();

    /**
     * add a new feed
     *
     * @access public
     * @param string $label
     * @param string $url
     * @param bool $prepare
     * @return Komaks_NewsEdit_Block_Rss
     * @author Ultimate Module Creator
     */
    public function addFeed($label, $url, $prepare = false)
    {
        $link = ($prepare ? $this->getUrl($url) : $url);
        $feed = new Varien_Object();
        $feed->setLabel($label);
        $feed->setUrl($link);
        $this->_feeds[$link] = $feed;
        return $this;
    }

    /**
     * get the current feeds
     *
     * @access public
     * @return array()
     * @author Ultimate Module Creator
     */
    public function getFeeds()
    {
        return $this->_feeds;
    }
}
