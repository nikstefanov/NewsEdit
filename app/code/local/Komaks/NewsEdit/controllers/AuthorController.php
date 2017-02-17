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
 * Author front contrller
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
class Komaks_NewsEdit_AuthorController extends Mage_Core_Controller_Front_Action
{

    /**
      * default action
      *
      * @access public
      * @return void
      * @author Ultimate Module Creator
      */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if (Mage::helper('komaks_newsedit/author')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb(
                    'home',
                    array(
                        'label' => Mage::helper('komaks_newsedit')->__('Home'),
                        'link'  => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'authors',
                    array(
                        'label' => Mage::helper('komaks_newsedit')->__('Authors'),
                        'link'  => '',
                    )
                );
            }
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', Mage::helper('komaks_newsedit/author')->getAuthorsUrl());
        }
        if ($headBlock) {
            $headBlock->setTitle(Mage::getStoreConfig('komaks_newsedit/author/meta_title'));
            $headBlock->setKeywords(Mage::getStoreConfig('komaks_newsedit/author/meta_keywords'));
            $headBlock->setDescription(Mage::getStoreConfig('komaks_newsedit/author/meta_description'));
        }
        $this->renderLayout();
    }

    /**
     * init Author
     *
     * @access protected
     * @return Komaks_NewsEdit_Model_Author
     * @author Ultimate Module Creator
     */
    protected function _initAuthor()
    {
        $authorId   = $this->getRequest()->getParam('id', 0);
        $author     = Mage::getModel('komaks_newsedit/author')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($authorId);
        if (!$author->getId()) {
            return false;
        } elseif (!$author->getStatus()) {
            return false;
        }
        return $author;
    }

    /**
     * view author action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function viewAction()
    {
        $author = $this->_initAuthor();
        if (!$author) {
            $this->_forward('no-route');
            return;
        }
        Mage::register('current_author', $author);
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if ($root = $this->getLayout()->getBlock('root')) {
            $root->addBodyClass('newsedit-author newsedit-author' . $author->getId());
        }
        if (Mage::helper('komaks_newsedit/author')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb(
                    'home',
                    array(
                        'label'    => Mage::helper('komaks_newsedit')->__('Home'),
                        'link'     => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'authors',
                    array(
                        'label' => Mage::helper('komaks_newsedit')->__('Authors'),
                        'link'  => Mage::helper('komaks_newsedit/author')->getAuthorsUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'author',
                    array(
                        'label' => $author->getName(),
                        'link'  => '',
                    )
                );
            }
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', $author->getAuthorUrl());
        }
        if ($headBlock) {
            if ($author->getMetaTitle()) {
                $headBlock->setTitle($author->getMetaTitle());
            } else {
                $headBlock->setTitle($author->getName());
            }
            $headBlock->setKeywords($author->getMetaKeywords());
            $headBlock->setDescription($author->getMetaDescription());
        }
        $this->renderLayout();
    }

    /**
     * authors rss list action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function rssAction()
    {
        if (Mage::helper('komaks_newsedit/author')->isRssEnabled()) {
            $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
            $this->loadLayout(false);
            $this->renderLayout();
        } else {
            $this->getResponse()->setHeader('HTTP/1.1', '404 Not Found');
            $this->getResponse()->setHeader('Status', '404 File not found');
            $this->_forward('nofeed', 'index', 'rss');
        }
    }
}
