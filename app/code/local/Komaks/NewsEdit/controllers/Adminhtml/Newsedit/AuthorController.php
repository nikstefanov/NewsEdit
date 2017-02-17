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
 * Author admin controller
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
class Komaks_NewsEdit_Adminhtml_Newsedit_AuthorController extends Komaks_NewsEdit_Controller_Adminhtml_NewsEdit
{
    /**
     * init the author
     *
     * @access protected
     * @return Komaks_NewsEdit_Model_Author
     */
    protected function _initAuthor()
    {
        $authorId  = (int) $this->getRequest()->getParam('id');
        $author    = Mage::getModel('komaks_newsedit/author');
        if ($authorId) {
            $author->load($authorId);
        }
        Mage::register('current_author', $author);
        return $author;
    }

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
        $this->_title(Mage::helper('komaks_newsedit')->__('News'))
             ->_title(Mage::helper('komaks_newsedit')->__('Authors'));
        $this->renderLayout();
    }

    /**
     * grid action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function gridAction()
    {
        $this->loadLayout()->renderLayout();
    }

    /**
     * edit author - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function editAction()
    {
        $authorId    = $this->getRequest()->getParam('id');
        $author      = $this->_initAuthor();
        if ($authorId && !$author->getId()) {
            $this->_getSession()->addError(
                Mage::helper('komaks_newsedit')->__('This author no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getAuthorData(true);
        if (!empty($data)) {
            $author->setData($data);
        }
        Mage::register('author_data', $author);
        $this->loadLayout();
        $this->_title(Mage::helper('komaks_newsedit')->__('News'))
             ->_title(Mage::helper('komaks_newsedit')->__('Authors'));
        if ($author->getId()) {
            $this->_title($author->getName());
        } else {
            $this->_title(Mage::helper('komaks_newsedit')->__('Add author'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * new author action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * save author - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost('author')) {
            try {
                $author = $this->_initAuthor();
                $author->addData($data);
                $author->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('komaks_newsedit')->__('Author was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $author->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setAuthorData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('komaks_newsedit')->__('There was a problem saving the author.')
                );
                Mage::getSingleton('adminhtml/session')->setAuthorData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('komaks_newsedit')->__('Unable to find author to save.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * delete author - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function deleteAction()
    {
        if ( $this->getRequest()->getParam('id') > 0) {
            try {
                $author = Mage::getModel('komaks_newsedit/author');
                $author->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('komaks_newsedit')->__('Author was successfully deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('komaks_newsedit')->__('There was an error deleting author.')
                );
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('komaks_newsedit')->__('Could not find author to delete.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * mass delete author - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massDeleteAction()
    {
        $authorIds = $this->getRequest()->getParam('author');
        if (!is_array($authorIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('komaks_newsedit')->__('Please select authors to delete.')
            );
        } else {
            try {
                foreach ($authorIds as $authorId) {
                    $author = Mage::getModel('komaks_newsedit/author');
                    $author->setId($authorId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('komaks_newsedit')->__('Total of %d authors were successfully deleted.', count($authorIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('komaks_newsedit')->__('There was an error deleting authors.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass status change - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massStatusAction()
    {
        $authorIds = $this->getRequest()->getParam('author');
        if (!is_array($authorIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('komaks_newsedit')->__('Please select authors.')
            );
        } else {
            try {
                foreach ($authorIds as $authorId) {
                $author = Mage::getSingleton('komaks_newsedit/author')->load($authorId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d authors were successfully updated.', count($authorIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('komaks_newsedit')->__('There was an error updating authors.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * export as csv - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportCsvAction()
    {
        $fileName   = 'author.csv';
        $content    = $this->getLayout()->createBlock('komaks_newsedit/adminhtml_author_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export as MsExcel - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportExcelAction()
    {
        $fileName   = 'author.xls';
        $content    = $this->getLayout()->createBlock('komaks_newsedit/adminhtml_author_grid')
            ->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export as xml - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportXmlAction()
    {
        $fileName   = 'author.xml';
        $content    = $this->getLayout()->createBlock('komaks_newsedit/adminhtml_author_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Check if admin has permissions to visit related pages
     *
     * @access protected
     * @return boolean
     * @author Ultimate Module Creator
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('cms/komaks_newsedit/author');
    }
}
