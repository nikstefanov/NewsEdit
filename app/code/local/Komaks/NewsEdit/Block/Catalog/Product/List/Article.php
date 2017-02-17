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
 * Article list on product page block
 *
 * @category    Komaks
 * @package     Komaks_NewsEdit
 * @author      Ultimate Module Creator
 */
class Komaks_NewsEdit_Block_Catalog_Product_List_Article extends Mage_Catalog_Block_Product_Abstract
{
    /**
     * get the list of articles
     *
     * @access protected
     * @return Komaks_NewsEdit_Model_Resource_Article_Collection
     * @author Ultimate Module Creator
     */
    public function getArticleCollection()
    {
        if (!$this->hasData('article_collection')) {
            $product = Mage::registry('product');
            $collection = Mage::getResourceSingleton('komaks_newsedit/article_collection')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->addAttributeToSelect('title', 1)
                ->addAttributeToFilter('status', 1)
                ->addProductFilter($product);
            $collection->getSelect()->order('related_product.position', 'ASC');
            $this->setData('article_collection', $collection);
        }
        return $this->getData('article_collection');
    }
}
