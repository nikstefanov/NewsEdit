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
 * Router
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
class Komaks_NewsEdit_Controller_Router extends Mage_Core_Controller_Varien_Router_Abstract
{
    /**
     * init routes
     *
     * @access public
     * @param Varien_Event_Observer $observer
     * @return Komaks_NewsEdit_Controller_Router
     * @author Ultimate Module Creator
     */
    public function initControllerRouters($observer)
    {
        $front = $observer->getEvent()->getFront();
        $front->addRouter('komaks_newsedit', $this);
        return $this;
    }

    /**
     * Validate and match entities and modify request
     *
     * @access public
     * @param Zend_Controller_Request_Http $request
     * @return bool
     * @author Ultimate Module Creator
     */
    public function match(Zend_Controller_Request_Http $request)
    {
        if (!Mage::isInstalled()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect(Mage::getUrl('install'))
                ->sendResponse();
            exit;
        }
        $urlKey = trim($request->getPathInfo(), '/');
        $check = array();
        $check['author'] = new Varien_Object(
            array(
                'prefix'        => Mage::getStoreConfig('komaks_newsedit/author/url_prefix'),
                'suffix'        => Mage::getStoreConfig('komaks_newsedit/author/url_suffix'),
                'list_key'      => Mage::getStoreConfig('komaks_newsedit/author/url_rewrite_list'),
                'list_action'   => 'index',
                'model'         =>'komaks_newsedit/author',
                'controller'    => 'author',
                'action'        => 'view',
                'param'         => 'id',
                'check_path'    => 0
            )
        );
        $check['article'] = new Varien_Object(
            array(
                'prefix'        => Mage::getStoreConfig('komaks_newsedit/article/url_prefix'),
                'suffix'        => Mage::getStoreConfig('komaks_newsedit/article/url_suffix'),
                'list_key'      => Mage::getStoreConfig('komaks_newsedit/article/url_rewrite_list'),
                'list_action'   => 'index',
                'model'         =>'komaks_newsedit/article',
                'controller'    => 'article',
                'action'        => 'view',
                'param'         => 'id',
                'check_path'    => 0
            )
        );
        $check['section'] = new Varien_Object(
            array(
                'prefix'        => Mage::getStoreConfig('komaks_newsedit/section/url_prefix'),
                'suffix'        => Mage::getStoreConfig('komaks_newsedit/section/url_suffix'),
                'list_key'      => Mage::getStoreConfig('komaks_newsedit/section/url_rewrite_list'),
                'list_action'   => 'index',
                'model'         =>'komaks_newsedit/section',
                'controller'    => 'section',
                'action'        => 'view',
                'param'         => 'id',
                'check_path'    => 1
            )
        );
        foreach ($check as $key=>$settings) {
            if ($settings->getListKey()) {
                if ($urlKey == $settings->getListKey()) {
                    $request->setModuleName('news')
                        ->setControllerName($settings->getController())
                        ->setActionName($settings->getListAction());
                    $request->setAlias(
                        Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
                        $urlKey
                    );
                    return true;
                }
            }
            if ($settings['prefix']) {
                $parts = explode('/', $urlKey);
                if ($parts[0] != $settings['prefix'] || count($parts) != 2) {
                    continue;
                }
                $urlKey = $parts[1];
            }
            if ($settings['suffix']) {
                $urlKey = substr($urlKey, 0, -strlen($settings['suffix']) - 1);
            }
            $model = Mage::getModel($settings->getModel());
            $id = $model->checkUrlKey($urlKey, Mage::app()->getStore()->getId());
            if ($id) {
                if ($settings->getCheckPath() && !$model->load($id)->getStatusPath()) {
                    continue;
                }
                $request->setModuleName('news')
                    ->setControllerName($settings->getController())
                    ->setActionName($settings->getAction())
                    ->setParam($settings->getParam(), $id);
                $request->setAlias(
                    Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
                    $urlKey
                );
                return true;
            }
        }
        return false;
    }
}
