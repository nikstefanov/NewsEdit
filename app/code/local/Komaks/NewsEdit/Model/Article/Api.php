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
class Komaks_NewsEdit_Model_Article_Api extends Mage_Api_Model_Resource_Abstract
{
    protected $_defaultAttributeList = array(
        'author_id', 
        'title', 
        'teaser', 
        'content', 
        'publication_date', 
        'image', 
        'status', 
        'url_key', 
        'in_rss', 
        'meta_title', 
        'meta_keywords', 
        'meta_description', 
        'allow_comment', 
        'created_at', 
        'updated_at', 
    );


    /**
     * init article
     *
     * @access protected
     * @param $articleId
     * @return Komaks_NewsEdit_Model_Article
     * @author      Ultimate Module Creator
     */
    protected function _initArticle($articleId)
    {
        $article = Mage::getModel('komaks_newsedit/article')->load($articleId);
        if (!$article->getId()) {
            $this->_fault('article_not_exists');
        }
        return $article;
    }

    /**
     * get articles
     *
     * @access public
     * @param mixed $filters
     * @return array
     * @author Ultimate Module Creator
     */
    public function items($filters = null)
    {
        $collection = Mage::getModel('komaks_newsedit/article')->getCollection()
            ->addAttributeToSelect('*');
        $apiHelper = Mage::helper('api');
        $filters = $apiHelper->parseFilters($filters);
        try {
            foreach ($filters as $field => $value) {
                $collection->addFieldToFilter($field, $value);
            }
        } catch (Mage_Core_Exception $e) {
            $this->_fault('filters_invalid', $e->getMessage());
        }
        $result = array();
        foreach ($collection as $article) {
            $result[] = $this->_getApiData($article);
        }
        return $result;
    }

    /**
     * Add article
     *
     * @access public
     * @param array $data
     * @return array
     * @author Ultimate Module Creator
     */
    public function add($data)
    {
        try {
            if (is_null($data)) {
                throw new Exception(Mage::helper('komaks_newsedit')->__("Data cannot be null"));
            }
            $data = (array)$data;
            if (isset($data['additional_attributes']) && is_array($data['additional_attributes'])) {
                foreach ($data['additional_attributes'] as $key=>$value) {
                    $data[$key] = $value;
                }
                unset($data['additional_attributes']);
            }
            $data['attribute_set_id'] = Mage::getModel('komaks_newsedit/article')->getDefaultAttributeSetId();
            $article = Mage::getModel('komaks_newsedit/article')
                ->setData((array)$data)
                ->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        } catch (Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return $article->getId();
    }

    /**
     * Change existing article information
     *
     * @access public
     * @param int $articleId
     * @param array $data
     * @return bool
     * @author Ultimate Module Creator
     */
    public function update($articleId, $data)
    {
        $article = $this->_initArticle($articleId);
        try {
            $data = (array)$data;
            if (isset($data['additional_attributes']) && is_array($data['additional_attributes'])) {
                foreach ($data['additional_attributes'] as $key=>$value) {
                    $data[$key] = $value;
                }
                unset($data['additional_attributes']);
            }
            $article->addData($data);
            $article->save();
        }
        catch (Mage_Core_Exception $e) {
            $this->_fault('save_error', $e->getMessage());
        }

        return true;
    }

    /**
     * remove article
     *
     * @access public
     * @param int $articleId
     * @return bool
     * @author Ultimate Module Creator
     */
    public function remove($articleId)
    {
        $article = $this->_initArticle($articleId);
        try {
            $article->delete();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('remove_error', $e->getMessage());
        }
        return true;
    }

    /**
     * get info
     *
     * @access public
     * @param int $articleId
     * @return array
     * @author Ultimate Module Creator
     */
    public function info($articleId)
    {
        $result = array();
        $article = $this->_initArticle($articleId);
        $result = $this->_getApiData($article);
        //related products
        $result['products'] = array();
        $relatedProductsCollection = $article->getSelectedProductsCollection();
        foreach ($relatedProductsCollection as $product) {
            $result['products'][$product->getId()] = $product->getPosition();
        }
        //related categories
        $result['categories'] = array();
        $relatedCategoriesCollection = $article->getSelectedCategoriesCollection();
        foreach ($relatedCategoriesCollection as $category) {
            $result['categories'][$category->getId()] = $category->getPosition();
        }
        //related sections
        $result['sections'] = array();
        $relatedSectionsCollection = $article->getSelectedSectionsCollection();
        foreach ($relatedSectionsCollection as $section) {
            $result['sections'][$section->getId()] = $section->getPosition();
        }
        return $result;
    }
    /**
     * Assign product to article
     *
     * @access public
     * @param int $articleId
     * @param int $productId
     * @param int $position
     * @return boolean
     * @author Ultimate Module Creator
     */
    public function assignProduct($articleId, $productId, $position = null)
    {
        $article = $this->_initArticle($articleId);
        $positions    = array();
        $products     = $article->getSelectedProducts();
        foreach ($products as $product) {
            $positions[$product->getId()] = array('position'=>$product->getPosition());
        }
        $product = Mage::getModel('catalog/product')->load($productId);
        if (!$product->getId()) {
            $this->_fault('product_not_exists');
        }
        $positions[$productId]['position'] = $position;
        $article->setProductsData($positions);
        try {
            $article->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return true;
    }

    /**
     * remove product from article
     *
     * @access public
     * @param int $articleId
     * @param int $productId
     * @return boolean
     * @author Ultimate Module Creator
     */
    public function unassignProduct($articleId, $productId)
    {
        $article = $this->_initArticle($articleId);
        $positions    = array();
        $products     = $article->getSelectedProducts();
        foreach ($products as $product) {
            $positions[$product->getId()] = array('position'=>$product->getPosition());
        }
        unset($positions[$productId]);
        $article->setProductsData($positions);
        try {
            $article->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return true;
    }

    /**
     * Assign category to article
     *
     * @access public
     * @param int $articleId
     * @param int $categoryId
     * @return boolean
     * @author Ultimate Module Creator
     */
    public function assignCategory($articleId, $categoryId)
    {
        $article = $this->_initArticle($articleId);
        $category   = Mage::getModel('catalog/category')->load($categoryId);
        if (!$category->getId()) {
            $this->_fault('category_not_exists');
        }
        $categories = $article->getSelectedCategories();
        $categoryIds = array();
        foreach ($categories as $category) {
            $categoryIds[] = $category->getId();
        }
        $categoryIds[] = $categoryId;
        $article->setCategoriesData($categoryIds);
        try {
            $article->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return true;
    }

    /**
     * remove category from article
     *
     * @access public
     * @param int $articleId
     * @param int $categoryId
     * @return boolean
     * @author Ultimate Module Creator
     */
    public function unassignCategory($articleId, $categoryId)
    {
        $article    = $this->_initArticle($articleId);
        $categories    = $article->getSelectedCategories();
        $categoryIds   = array();
        foreach ($categories as $key=>$category) {
            if ($category->getId() != $categoryId) {
                $categoryIds[] = $category->getId();
            }
        }
        $article->setCategoriesData($categoryIds);
        try {
            $article->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return true;
    }

    /**
     * Assign section to article
     *
     * @access public
     * @param int $articleId
     * @param int $sectionId
     * @return boolean
     * @author Ultimate Module Creator
     */
    public function assignSection($articleId, $sectionId)
    {
        $article = $this->_initArticle($articleId);
        $section   = Mage::getModel('komaks_newsedit/section')->load($sectionId);
        if (!$section->getId()) {
            $this->_fault('section_not_exists');
        }
        $sections = $article->getSelectedSections();
        $sectionIds = array();
        foreach ($sections as $section) {
            $sectionIds[] = $section->getId();
        }
        $sectionIds[] = $sectionId;
        $article->setSectionsData($sectionIds);
        try {
            $article->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return true;
    }

    /**
     * remove section from article
     *
     * @access public
     * @param int $articleId
     * @param int $sectionId
     * @return boolean
     * @author Ultimate Module Creator
     */
    public function unassignSection($articleId, $sectionId)
    {
        $article      = $this->_initArticle($articleId);
        $sections    = $article->getSelectedSections();
        $sectionIds  = array();
        foreach ($sections as $key=>$section) {
            if ($section->getId() != $sectionId) {
                $sectionIds[] = $section->getId();
            }
        }
        $article->setSectionsData($sectionIds);
        try {
            $article->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
        return true;
    }

    /**
     * Get list of additional attributes which are not in default create/update list
     *
     * @access public
     * @return array
     * @author Ultimate Module Creator
     */
    public function getAdditionalAttributes()
    {
        $entity = Mage::getModel('eav/entity_type')->load('komaks_newsedit_article', 'entity_type_code');
        $attributes = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setEntityTypeFilter($entity->getEntityTypeId());
        $result = array();
        foreach ($attributes as $attribute) {
            if (!in_array($attribute->getAttributeCode(), $this->_defaultAttributeList)) {
                if ($attribute->getIsGlobal() == Komaks_NewsEdit_Model_Attribute::SCOPE_GLOBAL) {
                    $scope = 'global';
                } elseif ($attribute->getIsGlobal() == Komaks_NewsEdit_Model_Attribute::SCOPE_WEBSITE) {
                    $scope = 'website';
                } else {
                    $scope = 'store';
                }

                $result[] = array(
                    'attribute_id' => $attribute->getId(),
                    'code'         => $attribute->getAttributeCode(),
                    'type'         => $attribute->getFrontendInput(),
                    'required'     => $attribute->getIsRequired(),
                    'scope'        => $scope
                );
            }
        }

        return $result;
    }

    /**
     * get data for api
     *
     * @access protected
     * @param Komaks_NewsEdit_Model_Article $article
     * @return array()
     * @author Ultimate Module Creator
     */
    protected function _getApiData(Komaks_NewsEdit_Model_Article $article)
    {
        $data = array();
        $additional = array();
        $additionalAttributes = $this->getAdditionalAttributes();
        $additionalByCode = array();
        foreach ($additionalAttributes as $attribute) {
            $additionalByCode[] = $attribute['code'];
        }
        foreach ($article->getData() as $key=>$value) {
            if (!in_array($key, $additionalByCode)) {
                $data[$key] = $value;
            } else {
                $additional[] = array('key'=>$key, 'value'=>$value);
            }
        }
        if (!empty($additional)) {
            $data['additional_attributes'] = $additional;
        }
        return $data;
    }
}
