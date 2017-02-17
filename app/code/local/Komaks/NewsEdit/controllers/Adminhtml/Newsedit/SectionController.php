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
 * Section admin controller
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
class Komaks_NewsEdit_Adminhtml_NewsEdit_SectionController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Initialize requested section and put it into registry.
     * Root section can be returned, if inappropriate store/section is specified
     *
     * @access protected
     * @param bool $getRootInstead
     * @return Komaks_NewsEdit_Model_Section
     * @author Ultimate Module Creator
     */
    protected function _initSection($getRootInstead = false)
    {
        $this->_title($this->__('News'))
             ->_title($this->__('Manage Sections'));
        $sectionId = (int) $this->getRequest()->getParam('id', false);
        $storeId    = (int) $this->getRequest()->getParam('store');
        $section = Mage::getModel('komaks_newsedit/section');
        $section->setStoreId($storeId);

        if ($sectionId) {
            $section->load($sectionId);
            if ($storeId) {
                $rootId = Mage::helper('komaks_newsedit/section')->getRootSectionId();
                if (!in_array($rootId, $section->getPathIds())) {
                    // load root section instead wrong one
                    if ($getRootInstead) {
                        $section->load($rootId);
                    } else {
                        $this->_redirect('*/*/', array('_current'=>true, 'id'=>null));
                        return false;
                    }
                }
            }
        }

        if ($activeTabId = (string) $this->getRequest()->getParam('active_tab_id')) {
            Mage::getSingleton('admin/session')->setSectionActiveTabId($activeTabId);
        }

        Mage::register('section', $section);
        Mage::register('current_section', $section);
        Mage::getSingleton('cms/wysiwyg_config')->setStoreId($this->getRequest()->getParam('store'));
        return $section;
    }

    /**
     * index action
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function indexAction()
    {
        $this->_forward('edit');
    }

    /**
     * Add new section form
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function addAction()
    {
        Mage::getSingleton('admin/session')->unsSectionActiveTabId();
        $this->_forward('edit');
    }

    /**
     * Edit section page
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function editAction()
    {
        $params['_current'] = true;
        $redirect = false;

        $storeId = (int) $this->getRequest()->getParam('store');
        $parentId = (int) $this->getRequest()->getParam('parent');
        $_prevStoreId = Mage::getSingleton('admin/session')
            ->getSectionLastViewedStore(true);

        if (!empty($_prevStoreId) && !$this->getRequest()->getQuery('isAjax')) {
            $params['store'] = $_prevStoreId;
            $redirect = true;
        }

        $sectionId = (int) $this->getRequest()->getParam('id');
        $_prevSectionId = Mage::getSingleton('admin/session')
            ->getLastEditedSection(true);


        if ($_prevSectionId
            && !$this->getRequest()->getQuery('isAjax')
            && !$this->getRequest()->getParam('clear')) {
             $this->getRequest()->setParam('id', $_prevSectionId);
        }

        if ($redirect) {
            $this->_redirect('*/*/edit', $params);
            return;
        }

        if ($storeId && !$sectionId && !$parentId) {
            $store = Mage::app()->getStore($storeId);
            $_prevSectionId = (int)Mage::helper('komaks_newsedit/section')->getRootSectionId();
            $this->getRequest()->setParam('id', $_prevSectionId);
        }

        if (!($section = $this->_initSection())) {
            return;
        }

        $this->_title($sectionId ? $section->getName() : $this->__('New Section'));

        $data = Mage::getSingleton('adminhtml/session')->getSectionData(true);
        if (isset($data['section'])) {
            $section->addData($data['section']);
        }

        /**
         * Build response for ajax request
         */
        if ($this->getRequest()->getQuery('isAjax')) {
            $breadcrumbsPath = $section->getPath();
            if (empty($breadcrumbsPath)) {
                $breadcrumbsPath = Mage::getSingleton('admin/session')->getSectionDeletedPath(true);
                if (!empty($breadcrumbsPath)) {
                    $breadcrumbsPath = explode('/', $breadcrumbsPath);
                    if (count($breadcrumbsPath) <= 1) {
                        $breadcrumbsPath = '';
                    } else {
                        array_pop($breadcrumbsPath);
                        $breadcrumbsPath = implode('/', $breadcrumbsPath);
                    }
                }
            }

            Mage::getSingleton('admin/session')
                ->setSectionLastViewedStore($this->getRequest()->getParam('store'));
            Mage::getSingleton('admin/session')
                ->setLastEditedSection($section->getId());
            $this->loadLayout();

            $eventResponse = new Varien_Object(
                array(
                    'content' => $this->getLayout()->getBlock('section.edit')->getFormHtml()
                        . $this->getLayout()->getBlock('section.tree')
                        ->getBreadcrumbsJavascript($breadcrumbsPath, 'editingSectionBreadcrumbs'),
                    'messages' => $this->getLayout()->getMessagesBlock()->getGroupedHtml(),
                )
            );

            Mage::dispatchEvent(
                'section_prepare_ajax_response',
                array(
                    'response' => $eventResponse,
                    'controller' => $this
                )
            );

            $this->getResponse()->setBody(
                Mage::helper('core')->jsonEncode($eventResponse->getData())
            );

            return;
        }

        $this->loadLayout();
        $this->_setActiveMenu('cms/komaks_newsedit/section');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true)
            ->setContainerCssClass('sections');

        $this->_addBreadcrumb(
            Mage::helper('komaks_newsedit')->__('Manage Sections'),
            Mage::helper('catalog')->__('Manage Sections')
        );

        $block = $this->getLayout()->getBlock('catalog.wysiwyg.js');
        if ($block) {
            $block->setStoreId($storeId);
        }

        $this->renderLayout();
    }

    /**
     * WYSIWYG editor action for ajax request
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function wysiwygAction()
    {
        $elementId = $this->getRequest()->getParam('element_id', md5(microtime()));
        $storeId = $this->getRequest()->getParam('store_id', 0);
        $storeMediaUrl = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);

        $content = $this->getLayout()->createBlock(
            'adminhtml/catalog_helper_form_wysiwyg_content',
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
     * Get tree node (Ajax version)
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function sectionsJsonAction()
    {
        if ($this->getRequest()->getParam('expand_all')) {
            Mage::getSingleton('admin/session')->setSectionIsTreeWasExpanded(true);
        } else {
            Mage::getSingleton('admin/session')->setSectionIsTreeWasExpanded(false);
        }
        if ($sectionId = (int) $this->getRequest()->getPost('id')) {
            $this->getRequest()->setParam('id', $sectionId);

            if (!$section = $this->_initSection()) {
                return;
            }
            $this->getResponse()->setBody(
                $this->getLayout()->createBlock('komaks_newsedit/adminhtml_section_tree')
                    ->getTreeJson($section)
            );
        }
    }

    /**
     * Section save
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function saveAction()
    {
        if (!$section = $this->_initSection()) {
            return;
        }

        $storeId = $this->getRequest()->getParam('store');
        $refreshTree = 'false';
        if ($data = $this->getRequest()->getPost()) {
            $section->addData($data['section']);
            if (!$section->getId()) {
                $parentId = $this->getRequest()->getParam('parent');
                if (!$parentId) {
                    $parentId = Mage::helper('komaks_newsedit/section')->getRootSectionId();
                }
                $parentSection = Mage::getModel('komaks_newsedit/section')->load($parentId);
                $section->setPath($parentSection->getPath());
            }

            /**
             * Process "Use Config Settings" checkboxes
             */
            if ($useConfig = $this->getRequest()->getPost('use_config')) {
                foreach ($useConfig as $attributeCode) {
                    $section->setData($attributeCode, null);
                }
            }

            $section->setAttributeSetId($section->getDefaultAttributeSetId());

            Mage::dispatchEvent(
                'komaks_newsedit_section_prepare_save',
                array(
                    'section' => $section,
                    'request' => $this->getRequest()
                )
            );

            $section->setData("use_post_data_config", $this->getRequest()->getPost('use_config'));

            try {
                $products = $this->getRequest()->getPost('section_products', -1);
                if ($products != -1) {
                    $productData = array();
                    parse_str($products, $productData);
                    $products = array();
                    foreach ($productData as $id => $position) {
                        $products[$id]['position'] = $position;
                    }
                    $section->setProductsData($productData);
                }
                $categories = $this->getRequest()->getPost('category_ids', -1);
                if ($categories != -1) {
                    $categories = explode(',', $categories);
                    $categories = array_unique($categories);
                    $section->setCategoriesData($categories);
                }
                $articles = $this->getRequest()->getPost('section_articles', -1);
                if ($articles != -1) {
                    $articleData = array();
                    parse_str($articles, $articleData);
                    foreach ($articleData as $id => $position) {
                        $article[$id]['position'] = $position;
                    }
                    $section->setArticlesData($articleData);
                }
                /**
                 * Check "Use Default Value" checkboxes values
                 */
                if ($useDefaults = $this->getRequest()->getPost('use_default')) {
                    foreach ($useDefaults as $attributeCode) {
                        $section->setData($attributeCode, false);
                    }
                }

                /**
                 * Unset $_POST['use_config'] before save
                 */
                $section->unsetData('use_post_data_config');

                $section->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('komaks_newsedit')->__('The section has been saved.')
                );
                $refreshTree = 'true';
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage())
                    ->setSectionData($data);
                $refreshTree = 'false';
            }
        }
        $url = $this->getUrl('*/*/edit', array('_current' => true, 'id' => $section->getId()));
        $this->getResponse()->setBody(
            '<script type="text/javascript">parent.updateContent("' . $url . '", {}, '.$refreshTree.');</script>'
        );
    }

    /**
     * Move section action
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function moveAction()
    {
        $section = $this->_initSection();
        if (!$section) {
            $this->getResponse()->setBody(
                Mage::helper('komaks_newsedit')->__('Section move error')
            );
            return;
        }
        $parentNodeId   = $this->getRequest()->getPost('pid', false);
        $prevNodeId     = $this->getRequest()->getPost('aid', false);

        try {
            $section->move($parentNodeId, $prevNodeId);
            $this->getResponse()->setBody("SUCCESS");
        } catch (Mage_Core_Exception $e) {
            $this->getResponse()->setBody($e->getMessage());
        } catch (Exception $e) {
            $this->getResponse()->setBody(
                Mage::helper('komaks_newsedit')->__('Section move error')
            );
            Mage::logException($e);
        }

    }

    /**
     * Delete section action
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function deleteAction()
    {
        if ($id = (int) $this->getRequest()->getParam('id')) {
            try {
                $section = Mage::getModel('komaks_newsedit/section')->load($id);
                Mage::dispatchEvent(
                    'komaks_newsedit_controller_section_delete',
                    array('section' => $section)
                );

                Mage::getSingleton('admin/session')->setSectionDeletedPath($section->getPath());

                $section->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('komaks_newsedit')->__('The section has been deleted.')
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->getResponse()->setRedirect($this->getUrl('*/*/edit', array('_current'=>true)));
                return;
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('komaks_newsedit')->__('An error occurred while trying to delete the section.')
                );
                $this->getResponse()->setRedirect($this->getUrl('*/*/edit', array('_current'=>true)));
                return;
            }
        }
        $this->getResponse()->setRedirect($this->getUrl('*/*/', array('_current'=>true, 'id'=>null)));
    }

    /**
     * Tree Action
     * Retrieve section tree
     *
     * @access public
     * @author Ultimate Module Creator
     */
    public function treeAction()
    {
        $storeId = (int) $this->getRequest()->getParam('store');
        $sectionId = (int) $this->getRequest()->getParam('id');

        if ($storeId) {
            if (!$sectionId) {
                $store = Mage::app()->getStore($storeId);
                $rootId = Mage::helper('komaks_newsedit/section')->getRootSectionId();
                $this->getRequest()->setParam('id', $rootId);
            }
        }

        $section = $this->_initSection();

        $block = $this->getLayout()->createBlock('komaks_newsedit/adminhtml_section_tree');
        $root  = $block->getRoot();
        $this->getResponse()->setBody(
            Mage::helper('core')->jsonEncode(
                array(
                    'data' => $block->getTree(),
                    'parameters' => array(
                        'text'         => $block->buildNodeName($root),
                        'draggable'    => false,
                        'allowDrop'    => ($root->getIsVisible()) ? true : false,
                        'id'           => (int) $root->getId(),
                        'expanded'     => (int) $block->getIsWasExpanded(),
                        'store_id'     => (int) $block->getStore()->getId(),
                        'section_id' => (int) $section->getId(),
                        'root_visible' => (int) $root->getIsVisible()
                    )
                )
            )
        );
    }

   /**
    * Build response for refresh input element 'path' in form
    *
    * @access public
    * @author Ultimate Module Creator
    */
    public function refreshPathAction()
    {
        if ($id = (int) $this->getRequest()->getParam('id')) {
            $section = Mage::getModel('komaks_newsedit/section')->load($id);
            $this->getResponse()->setBody(
                Mage::helper('core')->jsonEncode(
                    array(
                       'id' => $id,
                       'path' => $section->getPath(),
                    )
                )
            );
        }
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
        return Mage::getSingleton('admin/session')->isAllowed('cms/komaks_newsedit/section');
    }

    /**
     * get the products grid
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function productsgridAction()
    {
        if (!$section = $this->_initSection()) {
            return;
        }
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock(
                'komaks_newsedit/adminhtml_section_edit_tab_product',
                'section.product.grid'
            )
            ->toHtml()
        );
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
        $this->_initSection();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('komaks_newsedit/adminhtml_section_edit_tab_categories')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }

    /**
     * get child  action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function articlesgridAction()
    {
        if (!$section = $this->_initSection()) {
            return;
        }
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock(
                'komaks_newsedit/adminhtml_section_edit_tab_article',
                'section.article.grid'
            )
            ->toHtml()
        );
    }
}
