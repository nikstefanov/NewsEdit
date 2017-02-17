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
 * Article admin controller
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
class Komaks_NewsEdit_Adminhtml_Newsedit_ArticleController extends Mage_Adminhtml_Controller_Action
{
    /**
     * constructor - set the used module name
     *
     * @access protected
     * @return void
     * @see Mage_Core_Controller_Varien_Action::_construct()
     * @author Ultimate Module Creator
     */
    protected function _construct()
    {
        $this->setUsedModuleName('Komaks_NewsEdit');
    }

    /**
     * init the article
     *
     * @access protected 
     * @return Komaks_NewsEdit_Model_Article
     * @author Ultimate Module Creator
     */
    protected function _initArticle()
    {
        $this->_title($this->__('News'))
             ->_title($this->__('Manage Articles'));

        $articleId  = (int) $this->getRequest()->getParam('id');
        $article    = Mage::getModel('komaks_newsedit/article')
            ->setStoreId($this->getRequest()->getParam('store', 0));

        if ($articleId) {
            $article->load($articleId);
        }
        Mage::register('current_article', $article);
        return $article;
    }

    /**
     * default action for article controller
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function indexAction()
    {
        $this->_title($this->__('News'))
             ->_title($this->__('Manage Articles'));
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * new article action
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
     * edit article action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function editAction()
    {
        $articleId  = (int) $this->getRequest()->getParam('id');
        $article    = $this->_initArticle();
        if ($articleId && !$article->getId()) {
            $this->_getSession()->addError(
                Mage::helper('komaks_newsedit')->__('This article no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        if ($data = Mage::getSingleton('adminhtml/session')->getArticleData(true)) {
            $article->setData($data);
        }
        $this->_title($article->getTitle());
        Mage::dispatchEvent(
            'komaks_newsedit_article_edit_action',
            array('article' => $article)
        );
        $this->loadLayout();
        if ($article->getId()) {
            if (!Mage::app()->isSingleStoreMode() && ($switchBlock = $this->getLayout()->getBlock('store_switcher'))) {
                $switchBlock->setDefaultStoreName(Mage::helper('komaks_newsedit')->__('Default Values'))
                    ->setWebsiteIds($article->getWebsiteIds())
                    ->setSwitchUrl(
                        $this->getUrl(
                            '*/*/*',
                            array(
                                '_current'=>true,
                                'active_tab'=>null,
                                'tab' => null,
                                'store'=>null
                            )
                        )
                    );
            }
        } else {
            $this->getLayout()->getBlock('left')->unsetChild('store_switcher');
        }
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }

    /**
     * save article action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function saveAction()
    {
        $storeId        = $this->getRequest()->getParam('store');
        $redirectBack   = $this->getRequest()->getParam('back', false);
        $articleId   = $this->getRequest()->getParam('id');
        $isEdit         = (int)($this->getRequest()->getParam('id') != null);
        $data = $this->getRequest()->getPost();
        if ($data) {
            $article     = $this->_initArticle();
            $articleData = $this->getRequest()->getPost('article', array());
            $article->addData($articleData);
            $article->setAttributeSetId($article->getDefaultAttributeSetId());
                $products = $this->getRequest()->getPost('products', -1);
                if ($products != -1) {
                    $article->setProductsData(
                        Mage::helper('adminhtml/js')->decodeGridSerializedInput($products)
                    );
                }
                $categories = $this->getRequest()->getPost('category_ids', -1);
                if ($categories != -1) {
                    $categories = explode(',', $categories);
                    $categories = array_unique($categories);
                    $article->setCategoriesData($categories);
                }
                $sections = $this->getRequest()->getPost('section_ids', -1);
                if ($sections != -1) {
                    $sections = explode(',', $sections);
                    $sections = array_unique($sections);
                    $article->setSectionsData($sections);
                }
            if ($useDefaults = $this->getRequest()->getPost('use_default')) {
                foreach ($useDefaults as $attributeCode) {
                    $article->setData($attributeCode, false);
                }
            }
            try {
                $article->save();
                $articleId = $article->getId();
                $this->_getSession()->addSuccess(
                    Mage::helper('komaks_newsedit')->__('Article was saved')
                );
            } catch (Mage_Core_Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($e->getMessage())
                    ->setArticleData($articleData);
                $redirectBack = true;
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError(
                    Mage::helper('komaks_newsedit')->__('Error saving article')
                )
                ->setArticleData($articleData);
                $redirectBack = true;
            }
        }
        if ($redirectBack) {
            $this->_redirect(
                '*/*/edit',
                array(
                    'id'    => $articleId,
                    '_current'=>true
                )
            );
        } else {
            $this->_redirect('*/*/', array('store'=>$storeId));
        }
    }

    /**
     * delete article
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $article = Mage::getModel('komaks_newsedit/article')->load($id);
            try {
                $article->delete();
                $this->_getSession()->addSuccess(
                    Mage::helper('komaks_newsedit')->__('The articles has been deleted.')
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->getResponse()->setRedirect(
            $this->getUrl('*/*/', array('store'=>$this->getRequest()->getParam('store')))
        );
    }

    /**
     * mass delete articles
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massDeleteAction()
    {
        $articleIds = $this->getRequest()->getParam('article');
        if (!is_array($articleIds)) {
            $this->_getSession()->addError($this->__('Please select articles.'));
        } else {
            try {
                foreach ($articleIds as $articleId) {
                    $article = Mage::getSingleton('komaks_newsedit/article')->load($articleId);
                    Mage::dispatchEvent(
                        'komaks_newsedit_controller_article_delete',
                        array('article' => $article)
                    );
                    $article->delete();
                }
                $this->_getSession()->addSuccess(
                    Mage::helper('komaks_newsedit')->__('Total of %d record(s) have been deleted.', count($articleIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
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
        $articleIds = $this->getRequest()->getParam('article');
        if (!is_array($articleIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('komaks_newsedit')->__('Please select articles.')
            );
        } else {
            try {
                foreach ($articleIds as $articleId) {
                $article = Mage::getSingleton('komaks_newsedit/article')->load($articleId)
                    ->setStatus($this->getRequest()->getParam('status'))
                    ->setIsMassupdate(true)
                    ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d articles were successfully updated.', count($articleIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('komaks_newsedit')->__('There was an error updating articles.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
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
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * restrict access
     *
     * @access protected
     * @return bool
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     * @author Ultimate Module Creator
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('cms/komaks_newsedit/article');
    }

    /**
     * Export articles in CSV format
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportCsvAction()
    {
        $fileName   = 'articles.csv';
        $content    = $this->getLayout()->createBlock('komaks_newsedit/adminhtml_article_grid')
            ->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export articles in Excel format
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportExcelAction()
    {
        $fileName   = 'article.xls';
        $content    = $this->getLayout()->createBlock('komaks_newsedit/adminhtml_article_grid')
            ->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export articles in XML format
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportXmlAction()
    {
        $fileName   = 'article.xml';
        $content    = $this->getLayout()->createBlock('komaks_newsedit/adminhtml_article_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * wysiwyg editor action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function wysiwygAction()
    {
        $elementId     = $this->getRequest()->getParam('element_id', md5(microtime()));
        $storeId       = $this->getRequest()->getParam('store_id', 0);
        $storeMediaUrl = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);

        $content = $this->getLayout()->createBlock(
            'komaks_newsedit/adminhtml_newsedit_helper_form_wysiwyg_content',
            '',
            array(
                'editor_element_id' => $elementId,
                'store_id'          => $storeId,
                'store_media_url'   => $storeMediaUrl,
            )
        );
        $this->getResponse()->setBody($content->toHtml());
    }

    /**
     * mass author change - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massAuthorIdAction()
    {
        $articleIds = $this->getRequest()->getParam('article');
        if (!is_array($articleIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('komaks_newsedit')->__('Please select articles.')
            );
        } else {
            try {
                foreach ($articleIds as $articleId) {
                $article = Mage::getSingleton('komaks_newsedit/article')->load($articleId)
                    ->setAuthorId($this->getRequest()->getParam('flag_author_id'))
                    ->setIsMassupdate(true)
                    ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d articles were successfully updated.', count($articleIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('komaks_newsedit')->__('There was an error updating articles.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * get grid of products action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function productsAction()
    {
        $this->_initArticle();
        $this->loadLayout();
        $this->getLayout()->getBlock('article.edit.tab.product')
            ->setArticleProducts($this->getRequest()->getPost('article_products', null));
        $this->renderLayout();
    }

    /**
     * get grid of products action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function productsgridAction()
    {
        $this->_initArticle();
        $this->loadLayout();
        $this->getLayout()->getBlock('article.edit.tab.product')
            ->setArticleProducts($this->getRequest()->getPost('article_products', null));
        $this->renderLayout();
    }

    /**
     * get categories action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function categoriesAction()
    {
        $this->_initArticle();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * get child categories action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function categoriesJsonAction()
    {
        $this->_initArticle();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('komaks_newsedit/adminhtml_article_edit_tab_categories')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }

    /**
     * get  action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function sectionsAction()
    {
        $this->_initArticle();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * get child   action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function sectionsJsonAction()
    {
        $this->_initArticle();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('komaks_newsedit/adminhtml_article_edit_tab_section')
                ->getSectionChildrenJson($this->getRequest()->getParam('section'))
        );
    }
}
