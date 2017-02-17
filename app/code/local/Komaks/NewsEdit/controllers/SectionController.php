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
 * Section front contrller
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
class Komaks_NewsEdit_SectionController extends Mage_Core_Controller_Front_Action
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
        if (Mage::helper('komaks_newsedit/section')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb(
                    'home',
                    array(
                        'label' => Mage::helper('komaks_newsedit')->__('Home'),
                        'link'  => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'sections',
                    array(
                        'label' => Mage::helper('komaks_newsedit')->__('Sections'),
                        'link'  => '',
                    )
                );
            }
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', Mage::helper('komaks_newsedit/section')->getSectionsUrl());
        }
        if ($headBlock) {
            $headBlock->setTitle(Mage::getStoreConfig('komaks_newsedit/section/meta_title'));
            $headBlock->setKeywords(Mage::getStoreConfig('komaks_newsedit/section/meta_keywords'));
            $headBlock->setDescription(Mage::getStoreConfig('komaks_newsedit/section/meta_description'));
        }
        $this->renderLayout();
    }

    /**
     * init Section
     *
     * @access protected
     * @return Komaks_NewsEdit_Model_Section
     * @author Ultimate Module Creator
     */
    protected function _initSection()
    {
        $sectionId   = $this->getRequest()->getParam('id', 0);
        $section     = Mage::getModel('komaks_newsedit/section')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($sectionId);
        if (!$section->getId()) {
            return false;
        } elseif (!$section->getStatus()) {
            return false;
        }
        return $section;
    }

    /**
     * view section action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function viewAction()
    {
        $section = $this->_initSection();
        if (!$section) {
            $this->_forward('no-route');
            return;
        }
        if (!$section->getStatusPath()) {
            $this->_forward('no-route');
            return;
        }
        Mage::register('current_section', $section);
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if ($root = $this->getLayout()->getBlock('root')) {
            $root->addBodyClass('newsedit-section newsedit-section' . $section->getId());
        }
        if (Mage::helper('komaks_newsedit/section')->getUseBreadcrumbs()) {
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
                $breadcrumbBlock->addCrumb(
                    'home',
                    array(
                        'label'    => Mage::helper('komaks_newsedit')->__('Home'),
                        'link'     => Mage::getUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb(
                    'sections',
                    array(
                        'label' => Mage::helper('komaks_newsedit')->__('Sections'),
                        'link'  => Mage::helper('komaks_newsedit/section')->getSectionsUrl(),
                    )
                );
                $parents = $section->getParentSections();
                foreach ($parents as $parent) {
                    if ($parent->getId() != Mage::helper('komaks_newsedit/section')->getRootSectionId() &&
                        $parent->getId() != $section->getId()) {
                        $breadcrumbBlock->addCrumb(
                            'section-'.$parent->getId(),
                            array(
                                'label'    => $parent->getName(),
                                'link'    => $link = $parent->getSectionUrl(),
                            )
                        );
                    }
                }
                $breadcrumbBlock->addCrumb(
                    'section',
                    array(
                        'label' => $section->getName(),
                        'link'  => '',
                    )
                );
            }
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->addLinkRel('canonical', $section->getSectionUrl());
        }
        if ($headBlock) {
            if ($section->getMetaTitle()) {
                $headBlock->setTitle($section->getMetaTitle());
            } else {
                $headBlock->setTitle($section->getName());
            }
            $headBlock->setKeywords($section->getMetaKeywords());
            $headBlock->setDescription($section->getMetaDescription());
        }
        $this->renderLayout();
    }

    /**
     * sections rss list action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function rssAction()
    {
        if (Mage::helper('komaks_newsedit/section')->isRssEnabled()) {
            $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
            $this->loadLayout(false);
            $this->renderLayout();
        } else {
            $this->getResponse()->setHeader('HTTP/1.1', '404 Not Found');
            $this->getResponse()->setHeader('Status', '404 File not found');
            $this->_forward('nofeed', 'index', 'rss');
        }
    }

    /**
     * Submit new comment action
     * @access public
     * @author Ultimate Module Creator
     */
    public function commentpostAction()
    {
        $data   = $this->getRequest()->getPost();
        $section = $this->_initSection();
        $session    = Mage::getSingleton('core/session');
        if ($section) {
            if ($section->getAllowComments()) {
                if ((Mage::getSingleton('customer/session')->isLoggedIn() ||
                    Mage::getStoreConfigFlag('komaks_newsedit/section/allow_guest_comment'))) {
                    $comment  = Mage::getModel('komaks_newsedit/section_comment')->setData($data);
                    $validate = $comment->validate();
                    if ($validate === true) {
                        try {
                            $comment->setSectionId($section->getId())
                                ->setStatus(Komaks_NewsEdit_Model_Section_Comment::STATUS_PENDING)
                                ->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
                                ->setStores(array(Mage::app()->getStore()->getId()))
                                ->save();
                            $session->addSuccess($this->__('Your comment has been accepted for moderation.'));
                        } catch (Exception $e) {
                            $session->setSectionCommentData($data);
                            $session->addError($this->__('Unable to post the comment.'));
                        }
                    } else {
                        $session->setSectionCommentData($data);
                        if (is_array($validate)) {
                            foreach ($validate as $errorMessage) {
                                $session->addError($errorMessage);
                            }
                        } else {
                            $session->addError($this->__('Unable to post the comment.'));
                        }
                    }
                } else {
                    $session->addError($this->__('Guest comments are not allowed'));
                }
            } else {
                $session->addError($this->__('This section does not allow comments'));
            }
        }
        $this->_redirectReferer();
    }
}
